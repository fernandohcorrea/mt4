var Ssh = {
    
    ssh_connections_data : null,
    
    connectTest: function (btn)
    {
        var scope = this;

        $("#form-connection-id").ajaxForm({
            url: '/ssh/testconnection',
            type: 'post',
            clearForm: true,
            beforeSubmit: function () {
                $(btn).button('loading');
            },
            success: function (resp) {
                if (resp.status === true) {
                    scope.openSshTerm();
                    $('#modal-connection-id').modal('toggle');
                } else {
                    $('#modal-alert-id .msg').html(resp.msg);
                    $('#modal-alert-id').show();
                }
                $(btn).button('reset');
            },
            error: function(){
                $(btn).button('reset');
            }
        });

    },
    
    openSshTerm: function ()
    {
        var scope = this;
        $.ajax({
            type: "POST",
            url: '/ssh/loadconnections',
            dataType: 'json',
            success: function(resp){
                if (resp.status === true) {
                    scope.ssh_connection_data = resp.ssh_connection_data;
                    $('#input-connectionname-id').val(scope.ssh_connection_data.user + '@' + scope.ssh_connection_data.host);
                    $('#input-commandline-id').attr('disabled', false);
                    $('#btn-cmdexec-id').attr('disabled', false);
                    $('#input-commandline-id').val();
                } else {
                    alert('Falha ao abrir o Termina SSH');
                }
            }
        });

    },
    
    onSubmmitFormTerminalSsh : function(btn)
    {
        var scope = this;

        $("#form-terminalssh-id").ajaxForm({
            url: '/ssh/terminalcmd',
            type: 'post',
            clearForm: true,
            beforeSubmit: function () {
                $(btn).button('loading');
            },
            success: function (resp) {
                if (resp.status === true) {
                    $('#ssh-terminal-id').append(resp.result);
                    var wtf    = $('#ssh-terminal-id');
                    var height = wtf[0].scrollHeight;
                    wtf.scrollTop(height);
                } else {
                    alert('Erro ao executar command')
                }
                
                $(btn).button('reset');
            },
        });
        
    },
    
    ready: function ()
    {
        var scope = this;

        $("#form-connection-id button.btn-connect").click(function () {
            scope.connectTest(this);
        });
        
        $("#form-terminalssh-id button.btn-cmdexec").click(function () {
            scope.onSubmmitFormTerminalSsh(this);
        });
        
        
        
    }
};


$(document).ready(function () {
    Ssh.ready();
});