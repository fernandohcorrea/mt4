var Filesystem = {
    
    currentDirNode: null,
    rootDirNode : null,
    nodeListDir : [],
       
    loadFilesystem: function (node)
    {
        var node = node || null;
        
        var params = {
            'node' : node
        };
        
        var scope = this;
        $.ajax({
            type: "GET",
            url: '/filesystem/load',
            dataType: 'json',
            data: params,
            success: function(resp){
                if (resp.status === true) {
                    scope.buildBreadCrumbs(resp.currentDirNode);
                    $('ul.filesystem-lista').html('');
                    $('#template-file-id').tmpl(resp.list_files).appendTo('ul.filesystem-lista');
                    scope.linkEvents();
                } else {
                    alert('Fala ao abrir Carregar Arquivos');
                }
            }
        });

    },
    
    buildBreadCrumbs : function(currentDirNode)
    {
        this.currentDirNode = currentDirNode;
//        var found = false;
//        var nodesList = [];
//        
//        var l = this.nodeListDir.length;
//        
//        for(var i = 0 ; i < l; i++){           
//            var obj = this.nodeListDir[i];
//        }
//        
//        $('#list-breadcrumbs-id li').remove();
//        if(found){
            this.nodeListDir.push(currentDirNode);
            $('#template-linkbreadcrumbs-id').tmpl(currentDirNode).appendTo('#list-breadcrumbs-id');
        //}
               
        
    },
    
    linkEvents : function()
    {
        var scope = this;
        
        $('.file-acao-node').click(function(ev){
            ev.preventDefault();
            scope.onFileAcaoNode(this);
        });
        
        $('a.file-acao-check').click(function(ev){
            ev.preventDefault();
            scope.onFileAcaoCheck(this);
        });
        $('a.file-acao-delete').click(function(ev){
            ev.preventDefault();
            scope.onFileAcaoDelete(this);
        });
    },
    
    onFileAcaoNode : function(a)
    {
        var data = $(a).data();
        if(data.isdir){
            this.loadFilesystem(data.node);
        } else {
            window.location = '/filesystem/download/' + data.node;
        }
    },
    
    
    
    onFileAcaoCheck : function(flg)
    {
        
        var node = $(flg).data('node');
        var isdir = $(flg).data('isdir');
        var icon = $(flg).children('i')[0];
        var scope =  this;
        
        if(isdir == '1'){
            alert('Esse é um diretório');
            return;
        }
        
        scope.changeIconSt(icon, 'loading');
         
        var node = node || null;
        
        var params = {
            'node' : node
        };
        
        var scope = this;
        $.ajax({
            type: "GET",
            url: '/filesystem/check',
            dataType: 'json',
            data: params,
            success: function(resp){
                if (resp.status === true) {
                    scope.changeIconSt(icon, 'success');
                } else {
                    scope.changeIconSt(icon, 'fail');
                }
            }, 
            error : function(){
                scope.changeIconSt(icon, 'whaiting');
                alert('nao foi possuvel checar')
            }
        });
        
    },
    
    changeIconSt :  function(i , status)
    {
        $(i).removeClass('fa-circle-o-notch'); //loading
        $(i).removeClass('fa-spin'); //loading fa-spin
        
        $(i).removeClass('fa-circle-thin');    //normal
        
        $(i).removeClass('fa-check-circle-o'); //Aprovado
        
        $(i).removeClass('fa-exclamation-triangle'); //reprovado
        
        switch(status){
            case 'loading':
                $(i).addClass('fa-circle-o-notch').addClass('fa-spin');
                break;
                
                
            case 'success':
                $(i).addClass('fa-check-circle-o');
                break;
                
            case 'fail':
                $(i).addClass('fa-exclamation-triangle');
                break;
            
            case  'whaiting':
            default :
                $(i).addClass('fa-circle-thin');
                break;
        }
        
    },
    
    onFileAcaoDelete : function(flg)
    {
        var node = $(flg).data('node');
        var isdir = $(flg).data('isdir');
        var icon = $(flg).children('i')[0];
        var scope =  this;
        
        if(isdir == '1'){
            alert('Esse é um diretório');
            return;
        }
        
        scope.changeIconSt(icon, 'loading');
         
        var node = node || null;
        
        var params = {
            'node' : node
        };
        
        var scope = this;
        $.ajax({
            type: "GET",
            url: '/filesystem/delete',
            dataType: 'json',
            data: params,
            success: function(resp){
                if (resp.status === true) {
                    scope.loadFilesystem(scope.currentDirNode.node);
                }
            }, 
            error : function(){
                scope.changeIconSt(icon, 'whaiting');
                alert('nao foi possuvel checar')
            }
        });
    },
    
    uploadFile: function (btn)
    {
        var scope = this;
        
        var params = this.currentDirNode;
       
        var query_string = $.param(params);

        $("#form-upload-id").ajaxForm({
            url: '/filesystem/upload?' + query_string,
            type: 'post',
            clearForm: true,
            beforeSubmit: function () {
                $(btn).button('loading');
            },
            success: function (resp) {
                if (resp.status === true) {
                    scope.loadFilesystem(scope.currentDirNode.node);
                    $('#modal-upload-id').modal('toggle');
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
    
    ready: function ()
    {
        var scope = this;

        scope.loadFilesystem(null);
        
        $("#form-upload-id button.btn-connect").click(function () {
            scope.uploadFile(this);
        });
        
        $("#form-upload-id button.btn-connect").click(function () {
            scope.uploadFile(this);
        });
        
        $("#btn-checkall-id").click(function () {
            $('ul.filesystem-lista a.file-acao-check[data-isdir=false]').trigger('click');
        });
        
        
    }
};


$(document).ready(function () {
    Filesystem.ready();
});