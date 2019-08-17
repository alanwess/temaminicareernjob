<?php

  function tb_api_create_post($request) {
    $user = wp_get_current_user();

    if($user->ID > 0) {
        
      try {

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
        
          $category_names = explode(" / ",$request['localidade']);
          $cidade = $category_names[0];
          $estado = mb_strtoupper($category_names[1]);

          $numero_rand = 0;
          $string_codigo = "";
          for($i = 0; $i < 7 ; $i++){
            $numero_rand = rand(0,9);
            $string_codigo = $string_codigo.$numero_rand;
          }
          
          $title = 'Vaga: '.$request['titulo'].' ('.$cidade.'/'.$estado.') - Cod '.$string_codigo;

          $content = $request['descricao'];
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

          $cat_cidade = get_term_by('name', $cidade , 'category');
          $cat_estado = get_term_by('name', $estado , 'category');

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
          
          $article = array(
            'post_author' => $user->ID,
            'post_type' => 'post',
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_category' => array($cat_cidade_id),
            'tags_input' => $request['tags'],
            'comment_status' => 'open',
            'meta_input' => array(
              'codigo' => $request['codigo'],
              'link' => $request['link'],
              'origem' => 'Trabalha Brasil'
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

          return new WP_Error('vaga inserida', 'A vaga da requisição foi inserida.', array('status' => 200));
        } else {
          return new WP_Error('vaga repetida', 'A vaga da requisição já existe no site.', array('status' => 400));
        }

      } catch(Exception $e) {
        return new WP_Error('erro', $e->getMessage(), array('status' => 400));
      }

    } else {
      return new WP_Error('permissao', 'Usuário não possui permissão.', array('status' => 401));
    }
  }

  function registrar_tb_api_create_post() {
    register_rest_route('api', '/createjobtb', array(
      array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'tb_api_create_post',
      ),
    ));
  }
  add_action('rest_api_init', 'registrar_tb_api_create_post');

?>