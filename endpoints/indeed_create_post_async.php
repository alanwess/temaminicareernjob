<?php

  function indeed_api_create_post_async($request) {
    $user = wp_get_current_user();

    if($user->ID > 0) {

      global $wpdb;

      $sql = "
          SELECT posts.ID as PostID 
            FROM wp_posts posts
            INNER JOIN wp_postmeta meta
            ON posts.ID = meta.post_id
            WHERE meta.meta_key = 'codigo'
            AND meta.meta_value = '%s'
      ";

      $sql = $wpdb->prepare($sql, $request['codigo']);
      
      $wpdb->show_errors();
      $results = $wpdb->get_results($sql);    

      if (count($results) == 0){

        $category_names = explode(" / ", $request['localidade']);
        $cidade = trim($category_names[0]); 
        $estado = trim($category_names[1]);

        $numero_rand = 0;
        $string_codigo = "";
        for($i = 0; $i < 7 ; $i++){
          $numero_rand = rand(0,9);
          $string_codigo = $string_codigo.$numero_rand;
        }
        
        $title = 'Vaga: '.$request['titulo'].' ('.$cidade.'/'.$estado.') - Cod '.$string_codigo;

        if ($request['conteudo'] == ""){
           $content = $request['resumo'];
        } else {
           $content = $request['conteudo'];
        }

        $content .= '<br>';
        $content .= '<div class="post-meta"><p><i class="fa fa-lightbulb"></i> Dicas nossas para você enviar o seu currículo a um recrutador e se destacar ;) :

          • Na mensagem, seja breve.
          • Cumprimente com educação (bom dia/boa tarde/boa noite).
          • Informe se é para uma vaga em aberto ou para banco de dados.
          • Informe sua pretensão salarial, disponibilidade de horário e informações adicionais.
          • Evite informalidades e prefira ser respeitoso.
          • Não peça confirmação de recebimento do e-mail, o recrutador retornará se for necessário.
          • Só escreva o nome do responsável que receberá seu currículo se souber exatamente quem é e como se escreve.
          • NÃO cole as informações do currículo no e-mail, pois ele irá anexado.
          • Anexe o currículo á mensagem (preferencialmente em formato PDF e Word).</p></div>';
        $content.= '<br>';
        $content .= "<p>Confira todas as informações da vaga, clique abaixo e boa sorte! Sabemos que você tem enfrentado dificuldades por conta do desemprego, por isso que aqui em nosso portal todo conteúdo é GRÁTIS e não exige cadastro para ver as vagas. Queremos facilitar a sua vida :).</p>";

        $cat_cidade = get_term_by('slug', createSlug(tirarAcentos($cidade)) , 'category');
        $cat_estado = get_term_by('slug', createSlug(tirarAcentos($estado)) , 'category');

        if($cat_estado == false) {
          $cat = wp_insert_term($estado, 'category', array('slug' => createSlug(tirarAcentos($estado))));
          $cat_estado_id = $cat['term_id'];
        } else {
          $cat_estado_id = $cat_estado->term_id;
        }

        if($cat_cidade == false) {
          $cat = wp_insert_term($cidade, 'category', array('parent' => $cat_estado_id, 'slug' => createSlug(tirarAcentos($cidade))));
          $cat_cidade_id = $cat['term_id'];
        } else {
          $cat_cidade_id = $cat_cidade->term_id;
        }

        $tags = array();
        
        if ($request['empresa'] != "") array_push($tags, $request['empresa']);
        if ($request['salario'] != "") array_push($tags, $request['salario']);
        if ($request['area'] != "") array_push($tags, $request['area']); 
        if ($request['funcao'] != "") array_push($tags, $request['funcao']);
        if ($request['cidade'] != "") array_push($tags, $request['cidade']); 
        if ($request['estado'] != "") array_push($tags, $request['estado']);   
        
        $article = array(
          'post_author' => $user->ID,
          'post_type' => 'post',
          'post_title' => $title,
          'post_content' => $content,
          'post_status' => 'publish',
          'post_category' => array($cat_cidade_id),
          'tags_input' => $tags,
          'comment_status' => 'open',
          'meta_input' => array(
            'codigo' => $request['codigo'],
            'link' => $request['link'],
            'origem' => 'Indeed'
          )
        );

        $pid = wp_insert_post($article);

        if($pid){
          $thumbnail_id = 1556;
          set_post_thumbnail($pid, $thumbnail_id);

          $link = get_permalink($pid);
          if ($link != false) share_automatic_telegram($title, $link, $estado);
        }

        $vars = array_keys(get_defined_vars());
        for ($i = 0; $i < sizeOf($vars); $i++) {
            unset($$vars[$i]);
        }
        unset($vars,$i);

      }

    } 

  }

  function registrar_indeed_api_create_post_async() {
    register_rest_route('api', '/createjobindeedasync', array(
      array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'indeed_api_create_post_async',
      ),
    ));
  }
  add_action('rest_api_init', 'registrar_indeed_api_create_post_async');

?>