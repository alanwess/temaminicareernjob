<?php get_header(); //Template Name: Lead Grupos ?>

    <style type="text/css">

      select option[disabled] {
          font-weight: bold;
          background: rgba(100, 100, 100, 0.2);
      }

      #entragrupo{
        display: none;
      }

    </style>

    <main>
      <section>
        <div class="container text-center">

          <div id="step1">
            <h5>Selecione o grupo da sua região e entre no seu grupo de vagas no facebook ou telegram :)</h5>
            <div class="form-group">
              <label for="selecao-grupo">Selecione o grupo desejado:</label>
              <select id="selecao-grupo" class="form-control">
                <option>Selecione o grupo que deseja entrar</option>
                <?php
                  $categories = get_terms(array(
                      'taxonomy' => 'estado-regiao',
                      'parent'   => 0,
                      'orderby'  => 'name',
                      'order'    => 'ASC'
                    )
                  );

                  foreach( $categories as $category ) {
                    echo '<option disabled>'.$category->name.'</option>'; 
                    $args = array( 
                      'post_type' => 'grupo', 
                      'tax_query' => array( 
                        array( 
                          'taxonomy' => 'estado-regiao', 
                          'field' => 'slug', 
                          'terms' => $category->slug, 
                          'orderby' => 'name',
                          'order' => 'ASC'
                        ) 
                      ),
                      'orderby' => 'title'
                    );
                    $q = new WP_Query($args);
                    if ( $q->have_posts() ):
                        while ( $q->have_posts() ):
                          $q->the_post();
                          $meta_data = get_post_meta( $post->ID );

                          $option = '<option value="'.$meta_data['grupo_id'][0].'" class="'.$meta_data['disponibilidade_id'][0].'">&mdash; '.$category->name.' - '.$meta_data['grupo_nome_id'][0].'</option>';
                            echo $option;
                        endwhile;
                    endif;
                  } 

                ?>
              </select>
            </div>
            <button id="entragrupo" href="#" class="btn btn-info">Selecionar Grupo <i class="fa fa-arrow-right"></i></button>
            <hr>
            <h5>Caso deseje contribuir ou postar suas vagas nos nossos grupos parceiros clique logo abaixo :D</h5>
            <a href="<?= home_url( '/colaborar/') ?>" class="btn btn-info"><i class="fa fa-facebook"></i> Quero fazer parte!</a>
          </div>
          <div id="step2">
            <h5>Parabéns e seja bem vindo! Clique no botão logo abaixo da pagina para entrar em seu grupo agora mesmo! :D</h5>
            <p align="center">
                <a id="btnlinkgrupo" target="_blank" href="#" class="btn btn-info"><i class="fa fa-facebook"></i> Entrar no grupo</a>
            </p> 
          </div>

          <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
          <script>
            $(document).ready(function(){
              $('#pfb-signup-box-whatsapp').mask('(00) 00000 - 0000');
            });

            $(document).ready(function() {
               $('#selecao-grupo').change(function() {
                 $("#entragrupo").show();
               }); 
            });
           
            var linkgp = "";
            $("#entragrupo").click(function(){ 
                linkgp = $('#selecao-grupo').val();
                $("#btnlinkgrupo").attr('href',linkgp);
                $('#step1').hide();
                $('#step2').show();
            });

            $("#btnlinkgrupo").click(function(){
                window.open(linkgp,'_blank');
            });
          </script>

        </div>
       </section>
    </main>

<?php get_footer(); ?>