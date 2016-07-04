/*
 * ready方法
 */
var ready = function(callBack){

    /*
     * 1、高级浏览器判断
     * 2、IE678浏览器判断
     * 3、做兼容处理
     * 4、谁快用谁，用不同的方法处理
     * */
    var bReady = true;
    if(document.addEventListener) { //高级浏览器
        document.addEventListener('DOMContentLoaded',function(){ //绑定DOMContentLoaded 方法
            if(!bReady)return;
            bReady = false;
            callBack && callBack();//回调函数，存在调用
        },false);
        window.addEventListener('load',function(){ // 绑定onload方法
            if(!bReady)return;
            bReady = false;
            callBack && callBack();//回调函数，存在调用
        },false);
    }else if(document.attachEvent){ //ie678浏览器处理
        document.attachEvent('onreadystatechange',function(){
            if(!bReady) return;
            if(document.readyState == 'complete'){
                bReady = false;
                callBack && callBack();//回调函数，存在调用
            }
        });
        window.attachEvent('onload',function(){
            if(!bReady)return;
            bReady = false;
            callBack && callBack();//回调函数，存在调用
        });
        (!window.frameElement) && next();
        function next(){
            if(!bReady)return;
            try{
                document.documentElement.doScroll('left');
                bReady = false;
                callBack && callBack();//回调函数，存在调用
            }catch(e){
                setTimeout(next,1);
            }
        }
    }

}

ready( function(){
	var page_dom = document.getElementById('tr_page').getElementsByTagName('td')[0].getElementsByTagName('div')[0];
	page_dom.className="page";
    if( page_dom.getElementsByTagName('span').length > 0 ){
    	page_dom.getElementsByTagName('span')[0].style.display = "inline";

    	var dom_a_arr = page_dom.getElementsByTagName('a')
    	for(var i = 0;i< dom_a_arr.length;i++){

    		dom_a_arr[i].innerHTML = dom_a_arr[i].innerHTML.replace('&gt;&gt;', '下一页');
    		dom_a_arr[i].innerHTML = dom_a_arr[i].innerHTML.replace('&lt;&lt;', '上一页');
    	}
	}
} );