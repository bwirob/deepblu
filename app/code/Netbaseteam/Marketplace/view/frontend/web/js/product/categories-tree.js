/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    function sack(file) {
        this.xmlhttp = null;

        this.resetData = function() {
            this.method = "POST";
            this.queryStringSeparator = "?";
            this.argumentSeparator = "&";
            this.URLString = "";
            this.encodeURIString = true;
            this.execute = false;
            this.element = null;
            this.elementObj = null;
            this.requestFile = file;
            this.vars = new Object();
            this.responseStatus = new Array(2);
        };

        this.resetFunctions = function() {
            this.onLoading = function() { };
            this.onLoaded = function() { };
            this.onInteractive = function() { };
            this.onCompletion = function() { };
            this.onError = function() { };
            this.onFail = function() { };
        };

        this.reset = function() {
            this.resetFunctions();
            this.resetData();
        };

        this.createAJAX = function() {
            try {
                this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e1) {
                try {
                    this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e2) {
                    this.xmlhttp = null;
                }
            }

            if (! this.xmlhttp) {
                if (typeof XMLHttpRequest != "undefined") {
                    this.xmlhttp = new XMLHttpRequest();
                } else {
                    this.failed = true;
                }
            }
        };

        this.setVar = function(name, value){
            this.vars[name] = Array(value, false);
        };

        this.encVar = function(name, value, returnvars) {
            if (true == returnvars) {
                return Array(encodeURIComponent(name), encodeURIComponent(value));
            } else {
                this.vars[encodeURIComponent(name)] = Array(encodeURIComponent(value), true);
            }
        }

        this.processURLString = function(string, encode) {
            var encoded = encodeURIComponent(this.argumentSeparator);
            var regexp = new RegExp(this.argumentSeparator + "|" + encoded);
            var varArray = string.split(regexp);
            for (i = 0; i < varArray.length; i++){
                var urlVars = varArray[i].split("=");
                if (true == encode){
                    this.encVar(urlVars[0], urlVars[1]);
                } else {
                    this.setVar(urlVars[0], urlVars[1]);
                }
            }
        }

        this.createURLString = function(urlstring) {
            if (this.encodeURIString && this.URLString.length) {
                this.processURLString(this.URLString, true);
            }

            if (urlstring) {
                if (this.URLString.length) {
                    this.URLString += this.argumentSeparator + urlstring;
                } else {
                    this.URLString = urlstring;
                }
            }

            // prevents caching of URLString
            this.setVar("rndval", new Date().getTime());

            var urlstringtemp = new Array();
            for (key in this.vars) {
                if (false == this.vars[key][1] && true == this.encodeURIString) {
                    var encoded = this.encVar(key, this.vars[key][0], true);
                    delete this.vars[key];
                    this.vars[encoded[0]] = Array(encoded[1], true);
                    var key = encoded[0];
                }

                urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
            }
            if (urlstring){
                this.URLString += this.argumentSeparator + urlstringtemp.join(this.argumentSeparator);
            } else {
                this.URLString += urlstringtemp.join(this.argumentSeparator);
            }
        }

        this.runResponse = function() {
            eval(this.response);
        }

        this.runAJAX = function(urlstring) {
            if (this.failed) {
                this.onFail();
            } else {
                this.createURLString(urlstring);
                if (this.element) {
                    this.elementObj = document.getElementById(this.element);
                }
                if (this.xmlhttp) {
                    var self = this;
                    if (this.method == "GET") {
                        var totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
                        this.xmlhttp.open(this.method, totalurlstring, true);
                    } else {
                        this.xmlhttp.open(this.method, this.requestFile, true);
                        try {
                            this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
                        } catch (e) { }
                    }

                    this.xmlhttp.onreadystatechange = function() {
                        switch (self.xmlhttp.readyState) {
                            case 1:
                                self.onLoading();
                                break;
                            case 2:
                                self.onLoaded();
                                break;
                            case 3:
                                self.onInteractive();
                                break;
                            case 4:
                                self.response = self.xmlhttp.responseText;
                                self.responseXML = self.xmlhttp.responseXML;
                                self.responseStatus[0] = self.xmlhttp.status;
                                self.responseStatus[1] = self.xmlhttp.statusText;

                                if (self.execute) {
                                    self.runResponse();
                                }

                                if (self.elementObj) {
                                    var elemNodeName = self.elementObj.nodeName;
                                    elemNodeName.toLowerCase();
                                    if (elemNodeName == "input"
                                        || elemNodeName == "select"
                                        || elemNodeName == "option"
                                        || elemNodeName == "textarea") {
                                        self.elementObj.value = self.response;
                                    } else {
                                        self.elementObj.innerHTML = self.response;
                                    }
                                }
                                if (self.responseStatus[0] == "200") {
                                    self.onCompletion();
                                } else {
                                    self.onError();
                                }

                                self.URLString = "";
                                break;
                        }
                    };

                    this.xmlhttp.send(this.URLString);
                }
            }
        };

        this.reset();
        this.createAJAX();
    }
    $.widget('mage.marketplaceCategoriesTree',{
        options: {
            imageFolder : '',
            folderImage : 'folder-open.gif',
            plusImage : 'elbow-end-plus.gif',
            minusImage : 'elbow-end-minus.gif',
            initExpandedNodes:"",
            timeoutEdit : 20,
            editCounter : -1,
            editEl : false,
            categoryIds: ''
        },
        /*
         is to do some functions of control buttons
         */
        _create: function(){
            var seft = this;
            seft.ajax = new sack();
            var dhtmlgoodies_tree = document.getElementById('dhtmlgoodies_tree');
            var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');   // Get an array of all menu items
            for(var no=0;no<menuItems.length;no++){
                var subItems = menuItems[no].getElementsByTagName('UL');
                var img = document.createElement('IMG');
                img.src = seft.options.imageFolder + seft.options.plusImage;
                window.tempThis = seft;
                img.onclick = seft.showHideNode;
                seft.options = window.tempThis.options;
                if(subItems.length==0)img.style.visibility='hidden';
                var aTag = menuItems[no].getElementsByTagName('A')[0];

                if(aTag.id) var numericId = aTag.id.replace(/[^0-9]/g,'');else numericId = (no+1);

                aTag.id = 'dhtmlgoodies_treeNodeLink' + numericId;

                var input = document.createElement('INPUT');
                input.style.width = '200px';
                input.style.display='none';
                menuItems[no].insertBefore(input,aTag);
                input.id = 'dhtmlgoodies_treeNodeInput' + numericId;
                input.onblur = seft.hideEdit;

                menuItems[no].insertBefore(img,input);
                menuItems[no].id = 'dhtmlgoodies_treeNode' + numericId;
                aTag.onclick = seft.okToNavigate();
                aTag.onmousedown = seft.initEditLabel;
                var folderImg = document.createElement('IMG');
                if(menuItems[no].className){
                    folderImg.src = seft.options.imageFolder + menuItems[no].className;
                }else{
                    folderImg.src = seft.options.imageFolder + this.options.folderImage;
                }
                menuItems[no].insertBefore(folderImg,input);
            }

            seft.options.initExpandedNodes = seft.categoryIds;
            if(seft.options.initExpandedNodes){
                var nodes = seft.options.initExpandedNodes.split(',');
                for(var no=0;no<nodes.length;no++){
                    if(nodes[no]){
                        seft.explainNote(nodes[no]);
                    }
                }
            }

            document.documentElement.onmouseup = seft.mouseUpEvent();

            $('#expand-all').click(function(){
                var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
                for(var no=0;no<menuItems.length;no++){
                    var subItems = menuItems[no].getElementsByTagName('UL');
                    if(subItems.length>0 && subItems[0].style.display!='block'){
                        window.tempThis = seft;
                        seft.showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
                        seft.options = window.tempThis.options;
                    }
                }
                return false;
            });

            $('#collapse-all').click(function(){
                var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
                for(var no=0;no<menuItems.length;no++){
                    var subItems = menuItems[no].getElementsByTagName('UL');
                    if(subItems.length>0 && subItems[0].style.display=='block'){
                        window.tempThis = seft;
                        seft.showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
                        seft.options = window.tempThis.options;
                    }
                }
                return false;
            });
        },

        initEditLabel : function(){ },


        startEditLabel: function(){},



        showUpdate: function(){
            document.getElementById('ajaxMessage').innerHTML = this.ajax.response;
        },

        showHideNode: function(e,inputId) {
            if(inputId){
                if(!document.getElementById('dhtmlgoodies_treeNode'+inputId))return;
                var thisNode = document.getElementById('dhtmlgoodies_treeNode'+inputId).getElementsByTagName('IMG')[0];
            }else {
                var thisNode = this;
            }
            if(thisNode.style.visibility=='hidden')return;
            var parentNode = thisNode.parentNode;
            inputId = parentNode.id.replace(/[^0-9]/g,'');
            if(thisNode.src.indexOf('plus')>=0){
                thisNode.src = thisNode.src.replace('plus','minus');
                parentNode.getElementsByTagName('UL')[0].style.display='block';
                if(!tempThis.options.initExpandedNodes) tempThis.options.initExpandedNodes = ',';
                if(tempThis.options.initExpandedNodes.indexOf(',' + inputId + ',')<0) tempThis.options.initExpandedNodes = tempThis.options.initExpandedNodes + inputId + ',';

            }else{
                thisNode.src = thisNode.src.replace('minus','plus');
                parentNode.getElementsByTagName('UL')[0].style.display='none';
                tempThis.options.initExpandedNodes = tempThis.options.initExpandedNodes.replace(',' + inputId,'');
            }
        },


        hideEdit: function() {
            var editObj = this.options.editEl.previousSibling;
            if(editObj.value.length>0){
                this.options.editEl.innerHTML = editObj.value;
                this.ajax.requestFile = fileName + '?updateNode='+editObj.id.replace(/[^0-9]/g,'') + '&newValue='+editObj.value;    // Specifying which file to get
                this.ajax.onCompletion = this.showUpdate;   // Specify function that will be executed after file has been found
                this.ajax.runAJAX();        // Execute AJAX function

            }
            this.options.editEl.style.display='inline';
            editObj.style.display='none';
            this.options.editEl = false;
            this.options.editCounter=-1;
        },

        okToNavigate: function() {
            if(this.options.editCounter< 10)return true;
            return false;
        },

        explainNote: function(noteNumber) {
            var seft = this;
            while ($('#dhtmlgoodies_treeNode' + noteNumber).parent().attr('id') != 'dhtmlgoodies_tree') {
                noteNumber = $('#dhtmlgoodies_treeNode' + noteNumber).parent().parent().attr('lang');
                seft.showNode(false, noteNumber);
                seft.explainNote(noteNumber);

            }
        },

        mouseUpEvent: function() {
            this.options.editCounter =-1;
        },

        showNode: function(e,inputId) {
            if(inputId){
                if(!document.getElementById('dhtmlgoodies_treeNode'+inputId))return;
                var thisNode = document.getElementById('dhtmlgoodies_treeNode'+inputId).getElementsByTagName('IMG')[0];
            }else {
                var thisNode = this;
            }

            var parentNode = thisNode.parentNode;
            inputId = parentNode.id.replace(/[^0-9]/g,'');

            thisNode.src = thisNode.src.replace('plus','minus');
            parentNode.getElementsByTagName('UL')[0].style.display='block';
            if(!this.options.initExpandedNodes)this.options.initExpandedNodes = ',';
            if(this.options.initExpandedNodes.indexOf(',' + inputId + ',')<0) this.options.initExpandedNodes = this.options.initExpandedNodes + inputId + ',';
        }
    });
    return $.mage.marketplaceCategoriesTree;
});