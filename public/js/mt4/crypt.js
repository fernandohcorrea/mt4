var Crypt = {
    
      
    crypt: function ()
    {
        
        $("#frm-crypt-id").ajaxForm({
            url: '/crypt/crypt?',
            type: 'post',
            success: function (resp) {
                if (resp.status === true) {
                    $("#frm-crypt-id textarea[name=crypttext]").val(resp.data.crypttext);
                    
                } else {
                    alert("Falhou a Criptografia");
                }
            },
            error: function(){
                alert("Erro de Chamada");
            }
        });
        
        $("#frm-crypt-id").submit();
    },
    
    decrypt: function ()
    {
        $("#frm-crypt-id").ajaxForm({
            url: '/crypt/decrypt?',
            type: 'post',
            success: function (resp) {
                if (resp.status === true) {
                    $("#frm-crypt-id textarea[name=normaltext]").val(resp.data.normaltext);
                    
                } else {
                    alert("Falhou a Criptografia");
                }
            },
            error: function(){
                alert("Erro de Chamada");
            }
        });
        
        $("#frm-crypt-id").submit();
    },
    
     
    ready: function ()
    {
        var scope = this;

        
        $("#btn-crypt-id").click(function () {
            scope.crypt(this);
        });
        
        $("#btn-decrypt-id").click(function () {
            scope.decrypt(this);
        });
        
               
        
    }
};


$(document).ready(function () {
    Crypt.ready();
});