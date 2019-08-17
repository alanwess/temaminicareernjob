<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php get_titulo(); ?></title> 

    <?php $home = get_template_directory_uri(); ?>

    <link href="<?= $home ?>/assets/css/app.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="<?= $home ?>/assets/js/typed.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <link href='http://fonts.googleapis.com/css?family=Oswald:100,300,400,500,600,800%7COpen+Sans:300,400,500,600,700,800%7CMontserrat:400,700' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:100,300,400,500,600,800%7COpen+Sans:300,400,500,600,700,800%7CMontserrat:400,700' rel='stylesheet' type='text/css'>

    <style type="text/css">
      div#step2, div#step3, #responsive_slogan {
        display: none;
      }

      @media only screen and (max-width: 768px) {
        #responsive_slogan {
          display: block;
        }

        #noresponsive_slogan {
          display: none;
        }
      }

       .titulo{
        -webkit-border-radius: 24px 0; 
        -moz-border-radius: 24px 0; 
        border-radius: 24px 24px 24px 0; 
        -webkit-transform: rotate(-4deg); 
        padding-left: 10px; 
        padding-right: 10px; 
        background-color: #7ec855; 
        font-size: 12px; 
        color: #ffffff; 
        -webkit-box-shadow: 0px 5px 12px 0px rgba(50, 50, 50, 0.9);
        -moz-box-shadow: 0px 5px 12px 0px rgba(50, 50, 50, 0.9); 
        box-shadow: 0px 5px 12px 0px rgba(50, 50, 50, 0.9);
      }

      .linguagem {
        padding-left: 2px;
        padding-right: 3px;
        border-radius: 4px 4px 4px 0; 
        background-color: white;
        color: red;
      }
      
      .flex-container {
        padding: 0;
        margin: 0;
        list-style: none;
        -ms-box-orient: horizontal;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -moz-flex;
        display: -webkit-flex;
        display: flex;
      }

      .nowrap  { 
        -webkit-flex-wrap: nowrap;
        flex-wrap: nowrap;
      }

      .wrap    { 
        -webkit-flex-wrap: wrap;
        flex-wrap: wrap;
      }  

      .wrap-reverse         { 
        -webkit-flex-wrap: wrap-reverse;
        flex-wrap: wrap-reverse;
      }

      .ads-feed{
        margin-top: 30px;  
        display: block;
      }

      .ads-shadow{
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .05);
        transition: box-shadow .5s, background-color .5s, -webkit-box-shadow .5s;
      }

      @media (max-width: 991px){
        .ads-feed{
          margin-top: 20px; 
        }
      }
    </style>

    <?php wp_head(); ?>
  </head>
  
  <body class="nav-on-header smart-nav">
    
    <nav class="navbar">
      <div class="container" style="text-align: center;">

        <div class="pull-left">
          <div class="logo-wrapper">
            <div class="logo-alt" href="home">
              <a href="<?php echo home_url('/'); ?>"><p class="titulo">Career n' Job</p></a>
            </div>
            <div class="logo" href="home">
              <a href="<?php echo home_url('/'); ?>"><p class="titulo">C&J <span class="linguagem">pt-BR</span></p></a>
            </div>
          </div>
        </div>

        <ul class="nav-menu">
          <?php 
            $menu_name = 'Header Menu';
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
    </nav>
    
    <?php if(!is_singular(array('post')) && !is_author() && !is_category() && !is_search() && !is_page(array('Divulgador','Mobile'))): ?>
      <?php $count_posts = wp_count_posts('post')->publish; ?>
      <header class="site-header size-lg text-center" style="background-image: url(<?= $home ?>/assets/img/bg-banner1.jpeg)">
        <div class="container">
          <div class="col-xs-12">
              <?php if(!is_page('Home') && !is_404() && !is_search() && !is_page('Grupos')): ?>
                <h2>Você está em "<?php the_title(); ?>"</h2>
              <?php elseif(is_404()): ?>
                <h2>Nada encontrado nesse endereço :(</h2>
              <?php elseif(is_search()): ?>
                <h2>Veja os resultados abaixo para "<?= get_search_query(); ?>"</h2>
              <?php elseif(is_page('Grupos')): ?>
                <h2>Seja bem vindo caro Visitante :D</h2>
              <?php else: ?>
                <h1 class="text-left">Encontre seu emprego agora...</h1>
                <h6 class="font-alt" style="text-align: left;">Uma de nossas <?= $count_posts; ?> oportunidades com certeza é para você ;)</h6>
              <?php endif; ?>
            <?php if(is_page('Home') || is_404()): ?>
              <?php get_search_form(); ?>
            <?php endif; ?>
          </div>

        </div>
      </header>
    <?php endif; ?> 