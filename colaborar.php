<?php get_header(); //Template Name: Colaborar ?>

    <style type="text/css">
      #formvaga, #formgrupo, #formportfolio{
        display: none;
      }
    </style>

    <main id="faq-result"> 
      <section>
        <div class="container text-center">
          <header class="section-header" style="margin-top: 20px;">
            <span>Divulgue suas vagas nos nossos grupos</span>
            <h2>Contribuir com os grupos</h2>
          </header>

          <h6 id="textcontatogrupo">Deseja colaborar no grupo postando suas vagas ou compartilhando vagas, participe agora ;)</h6>
          <button id="contatoformgrupo" class="btn btn-info"><i class="fa fa-facebook"></i> Participar Agora!</button>

          <div id="formgrupo" class="form">
            <hr>
            <h5>Precisamos de algumas informações para continuar...</h5>
            <p>Por favor preencha as informações logo abaixo no formulário para que possamos te colocar nos grupos.</p>
            <form action="https://formspree.io/contact.careernjob@gmail.com" method="POST">
              <div class="form-group">
                <div class="input-group input-group-sm">
                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" name="Nome" class="form-control" placeholder="Seu nome completo" required>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group input-group-sm">
                  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                  <input type="email" name="Email" class="form-control" placeholder="seuemail@exemplo.com" required>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group input-group-sm">
                  <span class="input-group-addon"><i class="fa fa-whatsapp"></i></span>
                  <input type="phone" name="Whatsapp" class="form-control" placeholder="Seu Whatsapp (Ex: 11 967534567)" required>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group input-group-sm">
                  <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                  <select name="Cidade-regiao-interesse" class="form-control">
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
              </div>

              <input type="hidden" name="_next" value="<?= home_url('/obrigado/'); ?>" />
              <input type="hidden" name="_subject" value="Nova solicitação de colaboração" />
              <input type="hidden" name="_language" value="pt-BR" />

              <button type="submit" class="btn btn-info"><i class="fa fa-facebook"></i> Me coloque no grupo</button> 
              <br>
              <p><font size="1px">Obs: Em até 24 horas te colocaremos no grupo desejado ;)</font></p>
            </form>
          </div>
        </div>
      </section>
      <script>
          $("#contatoformgrupo").click(function(){ 
            $("#textcontatogrupo").hide();
            $("#contatoformgrupo").hide();
            $("#formgrupo").show();
          });
       </script>
    </main>

<?php get_footer(); ?>