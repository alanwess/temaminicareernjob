<?php get_header(); ?>

    <main>
      <section>
        <div class="container"> 
          <h3>404: Pagina não encontrada</h3>
          <h5>Lamentamos o ocorrido :(</h5>
          <p>Infelizmente o conteúdo que procura não está mais disponivel em nosso site.</p>
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