<?php get_header(); ?>

    <main>
      <section>
        <div class="container">
          <header class="section-header">
            <?php
              if(is_page('Home')):
                $paged = (get_query_var('page') ) ? get_query_var('page') : 1; 
                if($paged == 1):
            ?>
              <span>Recentemente</span>
              <h2>Vagas postadas</h2>
            <?php
                else:
            ?>
              <span>Recentemente</span>
              <h2>Pagina <?= $paged; ?></h2>
            <?php 
                endif; 
              endif;
            ?>
          </header>

          <div class="row">

            <?php

              $paged = (get_query_var('page') ) ? get_query_var('page') : 1;
              $args = array(
                'post_type'        => 'post',
                'orderby'          => 'date',
                'order'            => 'DESC',
                'posts_per_page'   => 20,
                'paged'            => $paged
              );

              $loop = new WP_Query($args);
              if($loop->have_posts()):
                while( $loop->have_posts() ):
                  $loop->the_post();
                  $vagas_meta_data = get_post_meta( $post->ID );  
            ?>

            <div class="col-xs-12">
              <a class="item-block" href="<?= the_permalink(); ?>">
                <header>
                  <div class="logo"><?php the_post_thumbnail(); ?></div> 
                  <div class="hgroup">
                    <h4><?php the_title(); ?></h4>
                    <h5><i class="fa fa-map-marker"></i> 
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

                <div class="item-body" style="word-wrap: break-word">
                  <p>
                    <?php 
                      $content = get_the_content(); 
                      echo str_replace('&nbsp;', ' ', strip_tags(mb_strimwidth($content, 0, 500, '...'))); 
                    ?>
                  </p>
                </div>

                <footer>
                  <ul class="details cols-1 text-right">
                    <li>
                      <i class="fa fa-arrow-right fa-2x"></i>
                    </li>
                  </ul>
                </footer>
              </a>
            </div>

            <?php
                  $count_int++;
                  if($count_int % 6 == 0):
            ?>
            <div>
              <div class="ads-feed col-xs-12 text-center">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle ads-shadow"
                     style="display:block"
                     data-ad-format="fluid"
                     data-ad-layout-key="-f1+2u+c7-9v-97"
                     data-ad-client="ca-pub-4807449657289752"
                     data-ad-slot="5647705551"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
              </div>
            </div>

            <?php
                  endif;
                endwhile;
              else:
                echo '<div class="container text-center"><h4>Nenhuma vaga a exibir no momento</h4></div>';
              endif;
            ?>
          </div>

          <?php 
            if($loop->max_num_pages > 1): 
          ?>
          <div class="row">
            <div class="col-xs-12">
              <nav style="margin-top: 20px;">
                <ul class="pager">
                  <li class="previous"><?= get_previous_posts_link( '<i class="ti-arrow-left"></i> Vagas anteriores'); ?></li>
                  <li class="next"><?= get_next_posts_link( 'Proximas Vagas <i class="ti-arrow-right"></i>' , $loop->max_num_pages); ?></li>
                </ul>
              </nav>
            </div>
          </div>
          <?php 
            endif; 
          ?>
        </div>
      </section>

      <section class="bg-img text-center" style="background-image: url(<?= get_template_directory_uri(); ?>/assets/img/bg-banner1.jpeg">
        <div class="container">
          <h2><strong>Inscreva-se</strong></h2>
          <h6 class="font-alt">Deixe-nos seu email e receba vagas e dicas exclusivas para sua vida profissional ;)</h6>
          <br><br>
          <form id="pfb-signup-submission" class="form-subscribe" action="#">
            <div class="input-group">
              <input id="pfb-signup-box-email" type="email" class="form-control input-lg" placeholder="Diga-nos seu email..." required>
              <span class="input-group-btn">
                <button id="pfb-signup-button" class="btn btn-success btn-lg" type="submit">Inscrever</button>
              </span>
            </div>
          </form>
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

<?php get_footer(); ?>