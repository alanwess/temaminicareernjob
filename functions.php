<?php

	global $s_informacoes;
	global $s_dados;

	include('botCareernjob/botWPAPICredentials.php');
	include('botCareernjob/botIndeed.php');
	include('botCareernjob/botTrabalhaBrasil.php');

	add_theme_support('post-thumbnails');
	add_theme_support('custom-fields');
  	add_theme_support('author');

  	function custom_menu_page_removing() {
		remove_menu_page( 'jetpack' );                    //Jetpack* 
		remove_menu_page( 'upload.php' );                 //Media
		remove_menu_page( 'edit-comments.php' );          //Comments
		remove_menu_page( 'themes.php' );                 //Appearance
		remove_menu_page( 'plugins.php' );                //Plugins
		remove_menu_page('edit.php?post_type=page');	  //Page
	}
	add_action( 'admin_menu', 'custom_menu_page_removing' );

	require_once(get_template_directory()."/endpoints/indeed_create_post.php");
  	require_once(get_template_directory()."/endpoints/indeed_create_post_async.php");
  	require_once(get_template_directory()."/endpoints/tb_create_post.php");
  	require_once(get_template_directory()."/endpoints/tb_create_post_async.php");

	function searchfilter($query) {
 
	    if ($query->is_search && !is_admin() ) {
	        $query->set('post_type',array('post'));
	        $query->set('paged', (get_query_var('paged')) ? get_query_var('paged') : 1 );
      		$query->set('posts_per_page',20);

      		if ($_REQUEST['localidade']){
      			$localidade = $_REQUEST['localidade'] ? $_REQUEST['localidade'] : '';

      			if (strpos(strtolower($localidade), 'região de ') !== false) {
				    $localidade = str_replace(substr($localidade,0,10), '', $localidade);
				}

      			$tax_query = array(
      				'relation' => 'OR',
                	array(
		                'taxonomy' => 'category',
		                'field'    => 'slug',
		                'terms'    => createSlug(tirarAcentos($localidade)),
	               	),
	               	array(
		                'taxonomy' => 'category',
		                'field'    => 'slug',
		                'terms'    => createSlug(tirarAcentos('Região de '.$localidade)),
	               	)
                );
                $query->set('tax_query', $tax_query);
            } 
	    }
	 
		return $query;
	}
	add_filter('pre_get_posts','searchfilter');

	function tags_pre_get_posts( $query )
	{
	    if(is_tag() && $query->is_main_query() ){
	    	$query->set('post_type', array('post'));
	        $query->set('paged', (get_query_var('paged')) ? get_query_var('paged') : 1);
	        $query->set('posts_per_page', 20);
	    }
	    return $query;
	}
	add_action( 'pre_get_posts','tags_pre_get_posts' );

	add_action( 'wp_ajax_hbgr_search', 'hbgr_search' );
	add_action( 'wp_ajax_nopriv_hbgr_search', 'hbgr_search' );
	function hbgr_search() {
	        $term = $_GET['term'];
	        $suggestions = array();

	        $input_args = array(
	            'post_type' => 'post',
	            's'         => $term
	        );

	        $loop = new WP_Query( $input_args);
	        while( $loop->have_posts() ) {
	            $loop->the_post();
	            $suggestion = array();
	            $suggestion['label'] = get_the_title();
	            $suggestion['link'] = get_permalink();

	            $suggestions[] = $suggestion;
	        }

	        //$suggestions = array_unique($suggestions, SORT_STRING);

	        wp_reset_postdata();

	        $response = wp_send_json($suggestions);

	}

	add_action( 'wp_ajax_hbgr_search_cat', 'hbgr_search_cat' );
	add_action( 'wp_ajax_nopriv_hbgr_search_cat', 'hbgr_search_cat' );
	function hbgr_search_cat() {
	        $suggestions = array();

			global $wpdb;

			$name = $wpdb->esc_like(stripslashes($_GET['term'])).'%'; //escape for use in LIKE statement
			$sql = "SELECT DISTINCT term.name as post_title FROM $wpdb->term_taxonomy tax 
					LEFT JOIN $wpdb->terms term ON term.term_id = tax.term_id WHERE 1 = 1 
					AND tax.taxonomy = 'category' 
                    AND term.name LIKE '%s'
					ORDER BY tax.count DESC
			";

			$sql = $wpdb->prepare($sql, $name);
			
		  	$wpdb->show_errors();
			$results = $wpdb->get_results($sql);     

			if (count($results)> 0){   //check if the result is empty
				//copy the titles to a simple array
				$suggestions = array();
				foreach( $results as $r ){
					$suggestion = array();
	            	$suggestion['label'] = $r->post_title;
	            	$suggestion['link'] = $r->post_title;
					
					$suggestions[] = $suggestion;
			    }
			}

	        wp_reset_postdata();

	        $response = wp_send_json($suggestions);

	}

	function get_categories_starting_with($word) {
		$categories = get_categories();
	    if (!empty($categories)) {
	        $relevant_ids = array();
	        foreach($categories as $c) {
	            $cat_name = $c->name;
	            if(substr($cat_name, 0, strlen($word)) == $word) { $relevant_ids[] = $c->cat_ID; }
	        }

	    	return $relevant_ids;
	    }
	}

	add_action( 'wp_enqueue_scripts', 'add_scripts' );
	function add_scripts() {
	    wp_enqueue_script( 'jquery' );
	    wp_enqueue_script( 'jquery-ui-autocomplete' );
	    wp_register_style( 'jquery-ui-styles','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
	    wp_enqueue_style( 'jquery-ui-styles' );
	    wp_register_script( 'my-autocomplete', get_template_directory_uri() . '/my-autocomplete.js', array( 'jquery', 'jquery-ui-autocomplete' ), '1.0', false );
	    wp_localize_script( 'my-autocomplete', 'MyAutocomplete', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	    wp_enqueue_script( 'my-autocomplete' );
	}

	function revcon_change_post_label() {
	    global $menu;
	    global $submenu;
	    $menu[5][0] = 'Vagas';
	    $submenu['edit.php'][5][0] = 'Vagas';
	    $submenu['edit.php'][10][0] = 'Adicionar vaga';
	}
	function revcon_change_post_object() {
	    global $wp_post_types;
	    $labels = &$wp_post_types['post']->labels;
	    $icon = &$wp_post_types['post']->menu_icon;
	    $icon = 'dashicons-businessman';
	    $labels->name = 'Vagas';
	    $labels->singular_name = 'Vaga';
	    $labels->add_new = 'Nova vaga';
	    $labels->add_new_item = 'Nova vaga';
	    $labels->edit_item = 'Editar vaga';
	    $labels->new_item = 'Vaga';
	    $labels->view_item = 'Ver vaga';
	    $labels->search_items = 'Buscar vaga';
	    $labels->not_found = 'Nenhuma vaga encontrada';
	    $labels->not_found_in_trash = 'Não há vagas na lixeira';
	    $labels->all_items = 'Todas as vagas';
	    $labels->menu_name = 'Vagas';
	    $labels->name_admin_bar = 'Vaga';
	}
	add_action( 'admin_menu', 'revcon_change_post_label' );
	add_action( 'init', 'revcon_change_post_object' );

	function revcon_change_cat_label() {
	    global $submenu;
	    $submenu['edit.php'][15][0] = 'Localidades'; // Rename categories to Authors
	}
	function revcon_change_cat_object() {
	    global $wp_taxonomies;
	    $labels = &$wp_taxonomies['category']->labels;
	    $labels->name = 'Localidade';
	    $labels->singular_name = 'Localidade';
	    $labels->add_new = 'Nova localidade';
	    $labels->add_new_item = 'Nova localidade';
	    $labels->edit_item = 'Editar localidade';
	    $labels->new_item = 'Localidade';
	    $labels->view_item = 'Ver localidade';
	    $labels->search_items = 'Procurar localidade';
	    $labels->not_found = 'Localidade não encontrada';
	    $labels->not_found_in_trash = 'Não há localidades na lixeira';
	    $labels->all_items = 'Todas localidades';
	    $labels->menu_name = 'Localidade';
	    $labels->name_admin_bar = 'Localidade';
	}
	add_action( 'init', 'revcon_change_cat_object' );
	add_action( 'admin_menu', 'revcon_change_cat_label' );

	function revcon_change_tag_label() {
	    global $submenu;
	   	$submenu['edit.php'][16][0] = 'Keywords'; // Rename categories to Authors
	}
	function revcon_change_tag_object() {
	    global $wp_taxonomies;
	    $labels = &$wp_taxonomies['post_tag']->labels;
	    $labels->name = 'Keyword';
	    $labels->singular_name = 'Keyword';
	    $labels->add_new = 'Nova Keyword';
	    $labels->add_new_item = 'Nova Keyword';
	    $labels->edit_item = 'Editar Keyword';
	    $labels->new_item = 'Keyword';
	    $labels->view_item = 'Ver Keyword';
	    $labels->search_items = 'Procurar Keyword';
	    $labels->not_found = 'Keyword não encontrada';
	    $labels->not_found_in_trash = 'Não há Keywords na lixeira';
	    $labels->all_items = 'Todas Keywords';
	    $labels->menu_name = 'Keyword';
	    $labels->name_admin_bar = 'Keyword';
	}
	add_action( 'init', 'revcon_change_tag_object' );
	add_action( 'admin_menu', 'revcon_change_tag_label' );

	function registrar_grupos() {
		$descricao = 'Usado para listar os grupos do Trampo';
		$singular = 'Grupo';
		$plural = 'Grupos';

		$labels = array(
			'name' => $plural,
			'singular_name' => $singular,
			'view_item' => 'Ver ' . $singular,
			'edit_item' => 'Editar ' . $singular,
			'new_item' => 'Novo ' . $singular,
			'add_new_item' => 'Adicionar novo ' . $singular
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail'
		);

		$args = array(
			'labels' => $labels,
			'description' => $descricao,
			'public' => true,
			'menu_icon' => 'dashicons-groups',
			'supports' => $supports
		);

		register_post_type( 'grupo', $args);	
	}
	add_action('init', 'registrar_grupos');

	function taxonomia_estado_regiao() {
		$singular = 'Estado-região';
		$plural = 'Estado-regiões';

		$labels = array(
			'name' => $plural,
			'singular_name' => $singular,
			'view_item' => 'Ver ' . $singular,
			'edit_item' => 'Editar ' . $singular,
			'new_item' => 'Nova ' . $singular,
			'add_new_item' => 'Adicionar nova ' . $singular
			);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true
			);

		register_taxonomy('estado-regiao', 'grupo', $args);
	}
	add_action( 'init' , 'taxonomia_estado_regiao' );

	function get_titulo($s_informacoes = "", $s_dados = "") {
		if(is_home() || is_front_page()) {
			echo 'Ajudando você a se encontrar no emprego certo para você';
			echo ' - ';
			bloginfo('name');
		} elseif (is_author()) {
			$author = get_queried_object();
    		$author_id = $author->ID;
    		echo get_author_name($author_id);
			echo ' - ';
			bloginfo('name');
		} elseif (is_search()){
			echo 'Buscando por resultados em "';
    		echo get_search_query();
			echo '" - ';
			bloginfo('name');
		} elseif (is_category()){
			$categoriaid = get_queried_object();
    		$categorianame = get_the_category_by_ID($categoriaid->term_id);
    		echo 'Pagina da categoria ';
    		echo $categorianame;
			echo ' - ';
			bloginfo('name');
		} elseif(is_404()){
			echo 'Nada encontrado neste endereço :(';
			echo ' - ';
			bloginfo('name');
		} elseif(is_page('Busca')){
			
			if ($s_informacoes == "" && $s_dados == "")
				echo 'Busca';
			elseif ($s_informacoes != "" && $s_dados == "")
				echo 'Buscando por "'.$s_informacoes.'"';
			elseif ($s_informacoes == "" && $s_dados != "")
				echo 'Buscando em "'.$s_dados.'"';
			else
				echo 'Buscando por "'.$s_informacoes.'" em "'.$s_dados.'"';

			echo ' - ';
			bloginfo('name');
		}else {
			the_title();
			echo ' - ';
			bloginfo('name');
		}
	}

    register_activation_hook(   __FILE__ , 't5_flush_rewrite_on_init' );
    register_deactivation_hook( __FILE__ , 't5_flush_rewrite_on_init' );
    add_action( 'init', 't5_page_to_seite' );

    function t5_page_to_seite()
    {
        $GLOBALS['wp_rewrite']->pagination_base = 'pagina';
    }

    function t5_flush_rewrite_on_init()
    {
        add_action( 'init', 'flush_rewrite_rules', 11 );
    }

    $new_page_content = 'Cansado de se candidatar em vários sites de emprego e sempre desanimar? Venha descobrir agora mesmo que é possível conhecer e vivenciar as experiências positivas ao ter contato com uma plataforma que integra o compartilhamento de oportunidades com a transmissão de vários conhecimentos que realmente visam agregar a vida profissional e acadêmica. Aqui você terá vagas e conteúdo sobre muitas áreas que podem te interessar... São mais de 40 categorias para que você encontre o que procura, onde além da tradicional busca de vagas, você pode encontrar conteúdos que melhorem seu perfil profissional e até mesmo pessoal. Somos uma equipe multiprofissional capacitada e que seleciona com qualidade e maestria seus conteúdos. Descubra agora mesmo!';

    $lista = array();
    array_push($lista, array("contato", "Contato", "contato.php"));
    array_push($lista, array("home", "Home", "front-page.php"));
    array_push($lista, array("privacidade", "Política de Privacidade", "privacidade.php"));
    array_push($lista, array("grupos", "Grupos", "lead-grupos.php"));
    array_push($lista, array("colaborar", "Colaborar", "colaborar.php"));
    array_push($lista, array("obrigado", "Obrigado", "email-recebido.php"));

    foreach ($lista as $itemlista) {
		if (isset($_GET['activated']) && is_admin()){
	  
	  		$new_page_name = $itemlista[0];
		    $new_page_title = $itemlista[1];
		    $new_page_template = $itemlista[2];
		    	  
		    $page_check = get_page_by_title($new_page_title);
		    $new_page = array(
		        'post_type' => 'page',
		        'post_name' => $new_page_name,
		        'post_title' => $new_page_title,
		        'post_content' => $new_page_content,
		        'post_status' => 'publish',
		        'post_author' => 1,
		    );
		    if(!isset($page_check->ID)){
		        $new_page_id = wp_insert_post($new_page);
		        if(!empty($new_page_template)){
		            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
		        }
		    }
		  
		}
	}

	$homepage = get_page_by_title('Home');
	if ($homepage){
	    update_option('page_on_front', $homepage->ID );
	    update_option('show_on_front', 'page' );
	}

	function criar_menu_header() {
		$menuname = 'Header Menu';
		$bpmenulocation = 'header_menu';
		$menu_exists = wp_get_nav_menu_object($menuname);

		if(!$menu_exists){
		    $menu_id = wp_create_nav_menu($menuname);

		    wp_update_nav_menu_item($menu_id, 0, array(
		        'menu-item-title' =>  __('Grupos'),
		        'menu-item-classes' => 'groups',
		        'menu-item-url' => home_url( '/grupos/' ), 
		        'menu-item-status' => 'publish'));

		    wp_update_nav_menu_item($menu_id, 0, array(
		        'menu-item-title' =>  __('Contato'),
		        'menu-item-classes' => 'forums',
		        'menu-item-url' => home_url( '/contato/' ), 
		        'menu-item-status' => 'publish'));

		    if(!has_nav_menu( $bpmenulocation ) ){
		        $locations = get_theme_mod('nav_menu_locations');
		        $locations[$bpmenulocation] = $menu_id;
		        set_theme_mod('nav_menu_locations', $locations );
		    }
		}
	}
	add_action('init', 'criar_menu_header');

	function criar_menu_footer() {
		$menuname = 'Footer Menu';
		$bpmenulocation = 'footer_menu';

		$menu_exists = wp_get_nav_menu_object($menuname);

		if(!$menu_exists){
		    $menu_id = wp_create_nav_menu($menuname);

		    wp_update_nav_menu_item($menu_id, 0, array(
		        'menu-item-title' =>  __('Grupos'),
		        'menu-item-classes' => 'groups',
		        'menu-item-url' => home_url( '/grupos/' ), 
		        'menu-item-status' => 'publish'));

		    wp_update_nav_menu_item($menu_id, 0, array(
		        'menu-item-title' =>  __('Contato'),
		        'menu-item-classes' => 'contato',
		        'menu-item-url' => home_url( '/contato/' ), 
		        'menu-item-status' => 'publish'));

		    wp_update_nav_menu_item($menu_id, 0, array(
		        'menu-item-title' =>  __('Privacidade'),
		        'menu-item-classes' => 'privacidade',
		        'menu-item-url' => home_url( '/privacidade/' ), 
		        'menu-item-status' => 'publish'));

		    if(!has_nav_menu( $bpmenulocation ) ){
		        $locations = get_theme_mod('nav_menu_locations');
		        $locations[$bpmenulocation] = $menu_id;
		        set_theme_mod('nav_menu_locations', $locations );
		    }
		}
	}
	add_action('init', 'criar_menu_footer');

	function enviar_email($email, $titulo, $mensagem) {
			return wp_mail($email, $titulo, $mensagem);
	}
	
	function scrapeImage($text) {
    	$pattern = '/src=[\'"]?([^\'" >]+)[\'" >]/';
    	preg_match($pattern, $text, $link);
    	$link = $link[1];
    	$link = urldecode($link);
    	return $link;
	}

	function createSlug($str, $delimiter = '-'){
    	$slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    	return $slug;
  	}    

  	function tirarAcentos($string){
    	return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
  	}

  	function url(){
	    return sprintf(
	      "%s://%s%s",
	      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
	      $_SERVER['SERVER_NAME'],
	      $_SERVER['REQUEST_URI']
	    );
  	}

  	function redirect($url) {
	    ob_start();
	    header('Location: '.$url);
	    ob_end_flush();
	    die();
  	}

  	function expire_token() {
	  return time() + (60 * 60 * 24);
	}
	add_action('jwt_auth_expire', 'expire_token');

	function currentYear(){
    	return date('Y');
	}
	add_shortcode( 'year', 'currentYear' );

	function start_botIndeed_estados(){

		$estados = array('Acre', 'Alagoas', 'Amazonas', 'Amapá', 'Bahia', 'Ceará', 'Distrito Federal', 'Espírito Santo', 'Goiás', 'Mato Grosso', 'Mato Grosso do Sul', 'Minas Gerais', 'Pará', 'Paraíba', 'Paraná', 'Pernambuco', 'Piauí', 'Rio de Janeiro', 'Rio Grande do Norte', 'Rondônia', 'Rio Grande do Sul', 'Roraima', 'Santa Catarina', 'Sergipe', 'São Paulo', 'Tocantins');
		$areas = array('Mais procuradas');
		$funcoes = array(
					array('Designer Especialista em UX', 'Desenvolvedor Mobile', 'Gerente de Educação Continuada em Serviços Clínicos', 'Especialista em Planejamento Financeiro', 'Analista de Desenvolvimento Organizacional', 'Office Manager', 'Supervisor de Planejamento e Controle de Produção', 'Analista de Compras', 'Executivo de Desenvolvimento de Negócios', 'Gerente de Acesso na Indústria Farmacêutica', 'Analista de BI', 'Analista de Planejamento Tributário', 'Especialista em Supply Chain', 'Gerente de Vendas', 'Analista de Trade Marketing', 'Analista de Compliance', 'Engenheiro de Vendas Técnicas', 'Agroecólogo', 'Analista de SEO', 'Advogado Especialista em Recuperação Judicial', 'Gerente de Mídias Sociais', 'Gestor de Operações Hospitalares', 'Analista de Segurança da Informação', 'Gerontologia', 'Gestor de Governança Corporativa', 'Analista Contábil Bilíngue', 'Diretor de Novos Negócios', 'Designer Especialista em UI', 'Biotecnólogo', 'Técnico em Mecatrônica')
					);

		$botIndeed = new botIndeed(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD, BOT_USER_VAGAS, BOT_PASS_VAGAS, WP_SITEURL);
		$botIndeed->setVagas_estados($estados, $areas, $funcoes);
		$botIndeed->insertWPVagas_estados();

	}
	add_action('hook_start_botIndeed_estados', 'start_botIndeed_estados');

	function finalize_botIndeed_estados(){

		$botIndeed = new botIndeed(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD, BOT_USER_VAGAS, BOT_PASS_VAGAS, WP_SITEURL);
		$botIndeed->insertWPVagas_estados();

	}
	add_action('hook_finalize_botIndeed_estados', 'finalize_botIndeed_estados');

	function start_botTrabalhaBrasil(){

		$botTrabalhaBrasil = new botTrabalhaBrasil(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD, BOT_USER_VAGAS, BOT_PASS_VAGAS, WP_SITEURL);

		$estados_url = $botTrabalhaBrasil->getEstadosArr('https://www.trabalhabrasil.com.br/busca-de-vagas');

		$botTrabalhaBrasil->setVagas($estados_url[array_rand($estados_url)]);
		$botTrabalhaBrasil->insertWPVagas();

	}
	add_action('hook_start_botTrabalhaBrasil', 'start_botTrabalhaBrasil');

	function finalize_botTrabalhaBrasil(){

		$botTrabalhaBrasil = new botTrabalhaBrasil(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD, BOT_USER_VAGAS, BOT_PASS_VAGAS, WP_SITEURL);
		$botTrabalhaBrasil->insertWPVagas();

	}
	add_action('hook_finalize_botTrabalhaBrasil', 'finalize_botTrabalhaBrasil');

	function share_automatic_telegram($title, $url, $estado){

		$bot = '835671953:AAGfOa4nS6sxoDGccrkLF8LmE5JtAiZo9_s';

		$chat_id = "";

		switch ($estado) {
			case 'AC':
				$chat_id = "-1001392651505";
				break;
			case 'AL':
				$chat_id = "-1001223412139";
				break;
			case 'AM':
				$chat_id = "-1001478483760";
				break;
			case 'AP':
				$chat_id = "-1001298120671";
				break;
			case 'BA':
				$chat_id = "-1001349839147";
				break;
			case 'CE':
				$chat_id = "-1001454769621";
				break;
			case 'DF':
				$chat_id = "-1001499779662";
				break;
			case 'ES':
				$chat_id = "-1001248597778";
				break;
			case 'GO':
				$chat_id = "-1001167831559";
				break;
			case 'MA':
				$chat_id = "-1001406577381";
				break;
			case 'MT':
				$chat_id = "-1001211866109";
				break;
			case 'MS':
				$chat_id = "-1001425759364";
				break;
			case 'MG':
				$chat_id = "-1001366449104";
				break;
			case 'PA':
				$chat_id = "-1001426220333";
				break;
			case 'PB':
				$chat_id = "-1001439099137";
				break;
			case 'PR':
				$chat_id = "-1001333640267";
				break;
			case 'PE':
				$chat_id = "-1001114173198";
				break;
			case 'PI':
				$chat_id = "-1001432947875";
				break;
			case 'RJ':
				$chat_id = "-1001477588019";
				break;
			case 'RN':
				$chat_id = "-1001157414240";
				break;
			case 'RO':
				$chat_id = "-1001307489846";
				break;
			case 'RS':
				$chat_id = "-1001467530279";
				break;
			case 'RR':
				$chat_id = "-1001176045786";
				break;
			case 'SC':
				$chat_id = "-1001187157650";
				break;
			case 'SE':
				$chat_id = "-1001434707040";
				break;
			case 'SP':
				$chat_id = "-1001404606611";
				break;		
			case 'TO':
				$chat_id = "-1001346079158";
				break;	
		}

		if ($chat_id != ""){

	    	$website = "https://api.telegram.org/bot".$bot;
			$params = [
			    'chat_id' => $chat_id,
			    'text' => "<b>".$title."</b> - ".$url,
			    'parse_mode' => 'html'
			];
			$ch = curl_init($website . '/sendMessage');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);
			curl_close($ch);
	    	
	    	return $result;
		    	
		}

	}

?>