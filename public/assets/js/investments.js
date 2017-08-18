

$(".edit-coin").click(function(){
    $.ajax({
        dataType: "json",
        url: '/investments/get/'+$(this).attr('id'),
        success: function(data){
        $("#amount").val(data["amount"]);
        $("#md_input_date").val(data["date_bought"]);
        $("#edit-form").attr('action', '/investments/edit/'+data["id"]);
    }
    });

});

    $(".sell-coin").click(function(){
        $("#sell-form").attr('action', '/investments/sell/'+$(this).attr('id'));
    });


    $("#import_ui").click(function(){
      $("#import2_modal").modal('toggle');
    });

    $("#exchangeinput").change(function(){

      if($(this).val() == "Bittrex")
      {
        $("#bittreximport").css('display', 'block');
      } else {
        $("#bittreximport").css('display', 'none');
      }

    });

    $("#import_polo").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        swal("Import added to queue", "You no longer have to wait for the import to complete, we have added the import to our queue and we will notify you on-site when it is completed!")
        var jqxhr = $.post( "/importpolo", {_token: CSRF_TOKEN }, function() {
        }).fail(function(){;
        });

    });


    $("#import_bittrex").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        swal("Import added to queue", "You no longer have to wait for the import to complete, we have added the import to our queue and we will notify you on-site when it is completed!")
        var jqxhr = $.post( "/importbittrex", {_token: CSRF_TOKEN }, function() {
        }).fail(function(){;
        });

    });



    $("#reset-data-polo").click(function(){
        swal({
          title: "Are you sure you want to reset Poloniex data?",
          text: "You will not be able to undo a reset.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, reset it!",
          closeOnConfirm: false
        },
        function(){
            window.location.replace("/polo/reset/");
        });
            });


            $("#reset-data-bittrex").click(function(){
                swal({
                  title: "Are you sure you want to reset Bittrex data?",
                  text: "You will not be able to undo a reset.",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, reset it!",
                  closeOnConfirm: false
                },
                function(){
                    window.location.replace("/bittrex/reset/");
                });
                    });



              $("#priceinput").change(function(){

                $("#total").css('display', 'none');
                $("#eur_per").css('display', 'none');
                $("#usd_per").css('display', 'none');
                $("#btc_per").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_per").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_per").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_per").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#total").css('display', '');
              }
            });

            $("#priceinputmulti").change(function(){

                $("#totalmulti").css('display', 'none');
                $("#eur_permulti").css('display', 'none');
                $("#usd_permulti").css('display', 'none');
                $("#btc_permulti").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_permulti").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_permulti").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_permulti").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totalmulti").css('display', '');
              }
            });


            $("#priceinputedit").change(function(){

                $("#totaledit").css('display', 'none');
                $("#eur_peredit").css('display', 'none');
                $("#usd_peredit").css('display', 'none');
                $("#btc_peredit").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_peredit").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_peredit").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_peredit").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totaledit").css('display', '');
              }
            });

            $("#priceinputsell").change(function(){

                $("#totalsell").css('display', 'none');
                $("#eur_persell").css('display', 'none');
                $("#usd_persell").css('display', 'none');
                $("#btc_persell").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_persell").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_persell").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_persell").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totalsell").css('display', '');
              }
            });

						$(".write_note_button").click(function(){
							var id = $(this).attr('id');
							var exchange = $(this).attr('exchange');

							$("#write-note-form").attr('action', '/investment/note/'+exchange+'/'+id);

						});

            var mySwiper = new Swiper('.swiper-container', {
              // Optional parameters
              direction: 'horizontal',
              loop: true,
            	observer: true,
            	observeParents: true,
		          keyboardControl: true,
		          paginationClickable: true,
              // If we need pagination
              pagination: '.swiper-pagination',

              // Navigation arrows
              nextButton: '.swiper-button-next',
              prevButton: '.swiper-button-prev'
            })
