$(document).ready(() => {
    function DataSearch(){
        $('#loader').show();
        $('#DataTableGetID').hide();$('#DataTableAlert').hide();
        $.ajax({
            type: 'POST',
            url: 'Controllers/Api.php',
            data: 'DataRefresh',
            success: Success,
            error: Error,
        });
        function Success(response){
            if(response.indexOf("Success") >= 0){
                setTimeout(() => {
                    $('#DataTableGetID').show();$('#DataTableAlert').show();
                    $('#loader').hide();
                    document.getElementById("DataReturn").innerHTML = response.replace("Success", '');
                }, 1000);
            }else if(response.indexOf("Error") >= 0){
                setTimeout(() => {
                    $('#DataTableGetID').show();$('#DataTableAlert').show();
                    $('#loader').hide();
                    Swal.fire("Hoydaa...", response.replace("Error", ''), "error");
                }, 1000);
            }
        }
        function Error(){
            setTimeout(() => {
                Swal.fire("Hoydaa...", "Sunucu Zorla Durduruldu, Lütfen Sonra Deneyin !", "warning");
                setTimeout(() => {
                    $('#DataTableGetID').show();$('#DataTableAlert').show();
                    $('#loader').hide();
                },1000);
            },1000);
        }
    }
    function CreditControl(){
        $.ajax({
            type: 'POST',
            url: 'Controllers/Api.php',
            data: 'CreditControl',
            success: SMSSuccess,
            error: SMSError,
        });
        function SMSSuccess(response){
            if(response.indexOf("Success") >= 0){
                $('#Crediİnput').html(response.replace("Success", ''));
            }else if(response.indexOf("Error") >= 0){
                setTimeout(() => {
                    Swal.fire("Hoydaa...", response.replace("Error", ''), "error");
                }, 1000);
            }
        }
        function SMSError(){
            setTimeout(() => {
                Swal.fire("Hoydaa...", "Sunucu Zorla Durduruldu, Lütfen Sonra Deneyin !", "warning");
            }, 1000);
        }
    }
    window.onload = DataSearch();CreditControl();
    $('#DataTableRefresh').click(() => {
        DataSearch(); CreditControl();
    });
    $(document).on("click", ".btn", function () {
        const smsPhone = $(this).attr('data-phone');
        Swal.fire({
            title: 'SMS Gönder',
            inputLabel: 'Telefon Numarası +'+smsPhone,
            input: 'textarea',
            inputPlaceholder: 'SMS Metnini Giriniz...',
            inputValue: 'Merhaba, Bu Bir Deneme Mesajdır...',
            inputAttributes: {
                maxlength: 160,
                autocorrect: 'off',
                autocapitalize: 'off'
            },
            confirmButtonText: 'Mesajı İlet <i class="fas fa-paper-plane"></i>',
            inputValidator: (value) => {
                if (!value) {
                    return 'SMS Metni Olmadan İşlem Yapılamaz !'
                }
            },
            showCancelButton: false
        }).then( (result) => {
            const smsValue = result.value;
            if(smsValue.length > 0){
                $.ajax({
                    type: 'POST',
                    url: 'Controllers/Api.php',
                    data: {smsValue:smsValue, smsPhone:smsPhone, DataPost:''},
                    success: SMSSuccess,
                    error: SMSError,
                });
                function SMSSuccess(response){
                    if(response.indexOf("Success") >= 0){
                        setTimeout(() => {
                            Swal.fire("Hey...", "SMS Gönderildi, MutluCell İle Keyifli Günler...", "success");
                            setTimeout(() => {
                                DataSearch(); CreditControl();
                            }, 5000);
                        }, 1000);
                    }else if(response.indexOf("Error") >= 0){
                        setTimeout(() => {
                            Swal.fire("Hoydaa...", response.replace("Error", ''), "error");
                        }, 1000);
                    }
                }
                function SMSError(){
                    setTimeout(() => {
                        Swal.fire("Hoydaa...", "Sunucu Zorla Durduruldu, Lütfen Sonra Deneyin !", "warning");
                    }, 1000);
                }
            }else{}
        })
    });
});