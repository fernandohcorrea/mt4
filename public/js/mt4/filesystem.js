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
                    alert('Fala ao abrir o Termina SSH');
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
    
    onFileAcaoCheck : function(a)
    {
        console.debug($(a).data());
    },
    
    onFileAcaoDelete : function(a)
    {
        console.debug($(a).data());
    },
    
    connectTest: function (btn)
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
            scope.connectTest(this);
        });
        
        
    }
};


$(document).ready(function () {
    Filesystem.ready();
});