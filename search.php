<?php get_header(); ?> 

    <header class="site-header size-lg text-center" style="background-image: url(<?= get_template_directory_uri(); ?>/assets/img/bg-banner1.jpeg);">
      <div class="container page-name">
        <h5 class="font-alt text-center">Encontre seu emprego agora mesmo!</h5>
        <?php get_search_form(); ?>
      </div>

      <div class="container">
        <form role="search" method="get" action="<?php echo home_url( '/' ); ?>">
          <?php 
            $param1 = array_key_exists('s', $_GET);
            $param2 = array_key_exists('localidade', $_GET);
            $check = 0;
            $controli = 0;
            $controll = 0;

            if($param1 || $param2){
              if($param1)
                if($_GET['s'] != ''){
                   $informacoes_query = $_GET['s'];
                   $controli = 1;
                }else{
                   $check++;
                }

              if($param2)
                if($_GET['localidade'] != ''){ 
                   $dados_query = $_GET['localidade'];
                   $controll = 1;
                }else{
                   $check++;
                }

                if($check == 2) echo '<h4>Todas as novas oportunidades foram listadas abaixo</h4>';
                else{
                  if($controli == 1 && $controll == 1) 
                    echo '<h4>Você pesquisou por "'.$informacoes_query.'" em "'.$dados_query.'"</h4>';
                  elseif ($controli == 1) 
                    echo '<h4>Você pesquisou por "'.$informacoes_query.'"</h4>';
                  elseif ($controll == 1) 
                    echo '<h4>Você pesquisou por "'.$dados_query.'"</h4>';
                }

            }
          ?>

        </form>

      </div>

    </header>

    <main>
      <section>
        <div class="container">
          <header class="section-header">
              <span>Resultados da busca</span>
              <?php
                  $paged = (get_query_var('paged') ) ? get_query_var('paged') : 1; 
                  if($paged > 1){
                    echo '<h2>Pagina '.$paged.'</h2>';
                  }
              ?>
          </header>

          <div class="row">

          <?php 
            if(have_posts()):
              while(have_posts()):
                the_post();
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
              if($param1 || $param2)
                echo '<h4 class="text-center" style="margin-left: 10px; margin-right: 10px;">Infelizmente nenhum item foi encontrado para a busca :(</h4>';
            endif;
          ?>
          </div>

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
        </div>
      </section>
    </main>

<?php get_footer(); ?>