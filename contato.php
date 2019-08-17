<?php get_header(); //Template Name: Contato ?>

    <main>
      <section>
        <div class="container">
          <div class="row">
            <div class="col-sm-12 col-md-8">
              <h4>Nos contate</h4>
              <form action="https://formspree.io/contact.careernjob@gmail.com" method="POST">
                <div class="form-group">
                  <input name="Nome" type="text" class="form-control input-lg" placeholder="Nome" required>
                </div>

                <div class="form-group">
                  <input name="_replyto" type="email" class="form-control input-lg" placeholder="Email" required>
                </div>

                <div class="form-group">
                  <textarea name="Mensagem" class="form-control" rows="5" placeholder="Mensagem" required></textarea>
                </div>

                <input type="hidden" name="_next" value="<?= home_url('/obrigado/'); ?>" />
                <input type="hidden" name="_subject" value="Nova solicitação de contato" />
                <input type="hidden" name="_language" value="pt-BR" />
                
                <button type="submit" class="btn btn-primary">Enviar</button>
              </form>
            </div>

            <div class="col-sm-12 col-md-4">
              <h4>Informação</h4>
              <div class="highlighted-block">
                <dl class="icon-holder">
                  <dt><i class="fa fa-envelope"></i></dt>
                  <dd><a href="mailto:contact.careernjob@gmail.com">contact.careernjob@gmail.com</a></dd>
                </dl>
              </div>
            </div>
          </div>

        </div>
      </section>
    </main>

<?php get_footer(); ?>