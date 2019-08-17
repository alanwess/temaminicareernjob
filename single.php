<?php get_header(); ?>

    <?php 
      if(have_posts()):
        while( have_posts() ):
          the_post();
          $vagas_meta_data = get_post_meta($post->ID);
          $link_facebook = get_the_permalink();
          $id_vaga = $post->ID;
    ?> 

      <header class="page-header bg-img size-lg" style="background-image: url(<?= get_template_directory_uri(); ?>/assets/img/bg-banner1.jpeg)">
        <div class="container">
          <div class="header-detail">
            <div class="logo"><?php the_post_thumbnail(); ?></div> 
            <div class="hgroup">
              <h1 style="word-wrap: break-word"><?php the_title(); ?></h1>
              <h3><?php 
                  $categories = get_the_category();
                  foreach ( $categories as $category ) { 
                      $category_id = $category->term_id;
                      $category_name = $category->name;
                      $category_parent = $category->parent;
                      $category_link = get_category_link($category_id);
                  }
                  echo '<i class="fa fa-map-marker"></i> <b>';
                  echo $category_name;
                  echo " / ";
                  $cat_pai = get_the_category_by_ID($category_parent);
                  echo $cat_pai.'</b>';
                  ?>
              </h3>
            </div>
            <hr>
            <p class="lead" style="word-wrap: break-word">
              <?php 
                      $content = get_the_content(); 
                      echo str_replace('&nbsp;', ' ', strip_tags(mb_strimwidth($content, 0, 500, '...'))); 
              ?>
            </p>

            <div class="button-group">
              <ul class="social-icons">
                <li class="title">Compartilhar no</li>
                <?php

                  $useragent=$_SERVER['HTTP_USER_AGENT'];

                  if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))):

                ?>
                <li><a class="whatsapp" href="whatsapp://send?text=*<?php the_title(); ?>*<?php if($vagas_meta_data['textarea_id'][0] == '') echo esc_attr($vagas_meta_data['textarea_id'][0] ); else echo esc_attr(' - '.$vagas_meta_data['textarea_id'][0] );?> - <?= the_permalink(); ?>"><i class="fa fa-whatsapp"></i></a></li>
                <?php else: ?>
                <li><a class="pinterest" href="http://pinterest.com/pin/create/button/?url=<?= the_permalink(); ?>&media=<?= get_the_post_thumbnail_url($post->ID,'full'); ?>"><i class="fa fa-pinterest"></i></a></li>
                <?php endif; ?>
                <li><a class="facebook" href="http://www.facebook.com/sharer.php?u=<?= the_permalink(); ?>"><i class="fa fa-facebook"></i></a></li>
                <li><a class="twitter" href="http://twitter.com/share?url=<?= the_permalink(); ?>&text=<?php the_title(); ?>"><i class="fa fa-twitter"></i></a></li>
                <li><a class="linkedin" href="javascript:void(0)" onclick="window.open( 'http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>', 'sharer', 'toolbar=0, status=0, width=626, height=436');return false;" title="<?php the_title(); ?>"><i class="fa fa-linkedin"></i></a></li>
                <?php

                  $useragent=$_SERVER['HTTP_USER_AGENT'];

                  if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))):

                ?>
                <li><a class="git" href="mailto:?subject=<?php the_title(); ?>&body=<?= esc_attr( $vagas_meta_data['textarea_id'][0] ); ?>%0D%0A %0D%0A Disponível em: <?= the_permalink(); ?>"><i class="fa fa-envelope"></i></a></li>
                <?php else: ?>
                <li><a class="google-plus" href="https://plus.google.com/share?url=<?= the_permalink(); ?>"><i class="fa fa-google-plus"></i></a></li>
                <li><a class="git" href="mailto:?subject=<?php the_title(); ?>&body=<?= esc_attr( $vagas_meta_data['textarea_id'][0] ); ?>%0D%0A %0D%0A Disponível em: <?= the_permalink(); ?>"><i class="fa fa-envelope"></i></a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </header>

      <div class="text-center">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Pre-content -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-4807449657289752"
             data-ad-slot="2327796935"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div>

      <main>
        <section>
          <div class="container">
            <div class="row">
              <div class="col-md-8 col-lg-9">
                <h1><?php the_title(); ?></h1>
                <div class="flex-container wrap">
                  <?php 
                    $tax_tags = get_the_tags();
                    if ($tax_tags != ''){
                      foreach($tax_tags as $tag){
                        echo '<span class="label" style="background-color: #29aafe; border-radius: 0px; margin-left: 0px; margin-right: 3px; margin-bottom: 3px; padding: 5px; text-transform: capitalize;">'.$tag->name.'</span>';
                      }
                    }
                  ?>
                </div>
                <h3>Detalhes da oportunidade:</h3>
                <style type="text/css">
                  .content-vaga{
                    word-wrap: break-word;
                    color: #7e8890 !important; 
                  }

                  .content-vaga p{
                    font-family: Open Sans, sans-serif !important; 
                    font-size: 15px !important;
                    color: #7e8890 !important;
                    line-height: 28px !important;
                  }

                  .content-vaga span{
                    font-family: Open Sans, sans-serif !important; 
                    font-size: 15px !important;
                    color: #7e8890 !important;
                    line-height: 28px !important;
                  }

                  .content-vaga li{
                    font-family: Open Sans, sans-serif !important; 
                    font-size: 15px !important;
                    color: #7e8890 !important;
                    line-height: 28px !important;
                  }
                </style>
                <div class="content-vaga"><?php 
                  $conteudo = get_the_content();
                  $conteudo = str_replace('•', '<br>•', $conteudo); 
                  echo $conteudo;
                ?></div>
                
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- Pos-content -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-4807449657289752"
                     data-ad-slot="6946054010"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>

                <div id="candidatar">  
                  <br>
                  <?php
                      $link = $vagas_meta_data['link'][0];
                  ?>
                    <h6>Clique no botão abaixo para acessar a vaga e se candidatar...</h6>
                    <a class="btn btn-primary" href="<?= $link; ?>">Me candidatar! <i class="fa fa-arrow-right"></i></a>
                    <br>
                    <small style="word-break: break-word;"><font size="1">* Você será redirecionado ao site do <?= $vagas_meta_data['origem'][0]; ?> *</font></small>
                </div>
                <div class="col-md-12" style="background-color: #fff; padding: 0px;">
                  <h6 id="vagasrelacionadas" class="widget-title">Já esta saindo? Confira isto</h6>
                  <?php if(rand(0, 1)): ?>
                    <div class="row">
                      <div class="col-xs-12 col-sm-4">
                        <img src="https://careernjob.com/wp-content/uploads/2019/08/f1b283da-c184-4797-b3d6-561d4f35ffa0.jpg" alt="" width="100%">
                      </div>
                      <div class="col-xs-12 col-sm-8">
                        <div class="hgroup">
                          <h4>Seu Perfil LinkedIn ficará TOP e será destaque!</h4>
                        </div>
                        <p class="lead">Aprenda tudo sobre as principais técnicas de engajamento para ser visto e lembrado por recrutadores e potenciais clientes. Você saberá como transmitir propósito de vida+propósito de negócios de maneira diferenciada, identificando facilmente se há a cultura desejada em oportunidades de emprego. Além do mais, descobrirá tudo sobre o funcionamento do algoritmo do LinkedIn.</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 col-xs-12" style="text-align: center; margin-top: 10px; margin-bottom: 30px;">
                        <a class="btn btn-danger" href="https://jessicabianca.com.br/pessoa/#aprimoramento-linkedin" style="width: 100%"><i class="fa fa-external-link"></i> Conferir</a>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="row">
                      <div class="col-xs-12 col-sm-4">
                        <img src="https://careernjob.com/wp-content/uploads/2019/08/26c13df4-9a63-4577-97a3-31d20fad820e.jpg" alt="" width="100%">
                      </div>
                      <div class="col-xs-12 col-sm-8">
                        <div class="hgroup">
                          <h4>Diga ADEUS à insegurança e medo em entrevistas!</h4>
                        </div>
                        <p class="lead">Aprenda tudo sobre a Entrevista por competências: estrutura mais utilizada por profissionais da área de Recrutamento e Seleção. Você ainda aprenderá técnicas para transmitir propósito de vida+negócio aos recrutadores, sabendo identificar se a empresa atende aos seus requisitos. Ainda descobrirá os segredos de um processo de recrutamento e seleção, incluindo feedback e período de experiência.</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-12 col-xs-12" style="text-align: center; margin-top: 10px; margin-bottom: 30px;">
                        <a class="btn btn-danger" href="https://jessicabianca.com.br/pessoa/#simulacao-de-entrevistas" style="width: 100%"><i class="fa fa-external-link"></i> Conferir</a>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="post-meta col-md-12" style="margin-bottom: 30px; margin-top: 0px;">
                  <form role="search" method="get" action="<?php echo home_url( '/' ); ?>">
                    <h3><i class="fa fa-bookmark"></i> Encontre resultados similares</h3>
                    <div class="form-group col-md-4">
                      <input type="text" id="s" name="s" class="form-control" style="border-radius: 3px" placeholder="<?php the_title(); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                      <input type="text" id="localidade" name="localidade" class="form-control" style="border-radius: 3px" placeholder="<?= $category_name; ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                      <button class="btn btn-primary" type="submit" style="width: 100%"><i class="fa fa-search"></i> Encontrar</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="col-md-4 col-lg-3">

                <style type="text/css">
                  .item-block:after{
                    margin-top: 0px !important;  
                  }

                  .item-block{
                    margin-top: 0px !important;  
                  }
                </style>

                <div class="widget">
                  <h6 id="vagasrelacionadas" class="widget-title">Vagas relacionadas</h6>
                  <div class="widget-body item-blocks-connected"> 
                  <?php

                    $taxonomy_args = array(
                      'relation' => 'OR',
                      array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => array(createSlug($category_name))
                      )
                    );

                    $args = array(
                      'post_type'        => 'post',
                      'tax_query'        => $taxonomy_args,
                      'posts_per_page'   => 6
                    );

                    $loop = new WP_Query($args);

                    if ($loop->post_count <= 1){

                      $taxonomy_args = array(
                        'relation' => 'OR',
                        array(
                          'taxonomy' => 'category',
                          'field' => 'slug',
                          'terms' => array(createSlug($category_name))
                        ),
                        array(
                          'taxonomy' => 'category',
                          'field' => 'slug',
                          'terms' => array(createSlug($cat_pai))
                        )
                      );

                      $args = array(
                        'post_type'        => 'post',
                        'tax_query'        => $taxonomy_args,
                        'posts_per_page'   => 6
                      );

                      $loop = new WP_Query( $args );

                      if ($loop->post_count <= 1){

                        $args = array(
                          'post_type'        => 'post',
                          'orderby'         => 'date',
                          'order'           => 'desc',
                          'posts_per_page'   => 6
                        );

                        $loop = new WP_Query( $args );
                        echo '<script>$("#vagasrelacionadas").html("Vagas recentes");</script>';
                      }

                    }

                    if( $loop->have_posts()): 
                      $count_anuncio = 0;
                      while( $loop->have_posts() ):
                        $loop->the_post();
                        $search_meta_data = get_post_meta( $post->ID );
                        if($post->ID != $id_vaga):
                  ?> 
                      <a class="item-block" href="<?= the_permalink(); ?>">
                        <header>
                          <div class="hgroup">
                            <h6><?= the_title(); ?></h6>
                            <h5>
                              <i class="fa fa-map-marker"></i> 
                              <?php 
                                $categories = get_the_category();
                                foreach ( $categories as $category ) { 
                                    $category_id = $category->term_id;
                                    $category_name = $category->name;
                                    $category_parent = $category->parent;
                                }

                                echo $category_name;
                                echo " / ";
                                $cat_pai = get_the_category_by_ID($category_parent);
                                echo $cat_pai;
                              ?>
                            </h5>
                          </div>
                        </header>
                      </a>     
                  <?php 
                        endif;
                      endwhile;
                    endif;
                  ?>  
                  </div>
                </div>

                <div class="widget">
                  <h6 class="widget-title">Conteúdo VIP</h6>
                  <div class="widget-body">
                    <div class="team-member">
                      <h5>Seja nosso assinante GRATIS!</h5>
                      <p>Insira seu endereço de email abaixo e receba as novidades e vagas gratuitamente no seu email ;)</p>
                      <form id="pfb-signup-submission" class="form-subscribenews" action="#">
                        <div class="input-group">
                          <input id="pfb-signup-box-email" type="email" class="form-control input-lg" placeholder="Diga-nos seu email..." required>
                          <button id="pfb-signup-button" class="btn btn-info" type="submit">Inscrever</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <script>
        $('#pfb-signup-submission').submit(function(event) {
          event.preventDefault();

          // Get data from form and store it
          var pfbSignupFNAME = 'Querido';
          var pfbSignupLNAME = 'Visitante';
          var pfbSignupEMAIL = $('#pfb-signup-box-email').val();

          // Create JSON variable of retreived data
          var pfbSignupData = {
            'firstname': pfbSignupFNAME,
            'lastname': pfbSignupLNAME,
            'email': pfbSignupEMAIL
          };

          // Send data to PHP script via .ajax() of jQuery
          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo get_template_directory_uri()."/mailchimpsignup.php"; ?>',
            data: pfbSignupData,
            beforeSend: function(){
               $("#pfb-signup-button").html('Inscrevendo...');
            },
            success: function (results) {
              $('#pfb-signup-box-email').attr('disabled',true);
              console.log(results);
              $("#pfb-signup-button").html('Inscrito <i class="fa fa-check"></i>');
              $('#pfb-signup-button').attr('disabled',true);
            },
            error: function (results) {
              window.alert('Nos desculpe, ocorreu um erro ao tentar te adicionar na lista de amigos :(');
              console.log(results);
            }
          });
        });
      </script>
      </main>

    <?php
        endwhile;
      endif;
    ?>

<?php get_footer(); ?>