<?php wp_footer(); ?>

  <footer class="site-footer">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-9">
          <h6>Outras vagas</h6>
          <ul class="footer-links">
            <?php

              $args = array(
                'post_type' => 'post',
                'posts_per_page' => 6,
                'orderby' => 'rand'
              );

              $loop = new WP_Query( $args );
              if( $loop->have_posts()):
                while( $loop->have_posts() ):
                  $loop->the_post();
                  $vagas_meta_data = get_post_meta( $post->ID );  
            ?>
              <li><a href="<?= the_permalink(); ?>"><?= the_title(); ?></a></li>
            <?php
                endwhile;
              endif;
            ?>
          </ul>
        </div>

        <div class="col-xs-6 col-md-3">
          <h6>Links</h6>
          <ul class="footer-links">
            <?php 
              $menu_name = 'Footer Menu';
              $menu_obj = get_term_by('name', $menu_name, 'nav_menu');
              $menu_id = $menu_obj->term_id;
              $menu = wp_get_nav_menu_object($menu_id);
              $menu_items = wp_get_nav_menu_items($menu->term_id);

              foreach ($menu_items as $menu_item) {
                  echo '<li><a href="'.$menu_item->url.'">'.$menu_item->title.'</a></li>';
              }
          ?>
          </ul>
        </div>

      </div>

      <hr>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-md-8 col-sm-6 col-xs-12">
          <p class="copyright-text">Copyright &copy; 2018-<?= date('Y'); ?> - <a href="https://www.careernjob.com">Careernjob</a></p>
        </div>
      </div>
    </div>
  </footer>
  </body>
</html>