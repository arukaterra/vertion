	var w = 0;
	var timeouts ; 
	var test;
	var uploadlabel = 0; 
	$(document).on('click','.plusNewHome',function(){
			
	
		if(uploadlabel==1){
			
			// $(".uploadlabel").html(locale.uploadlabel.post);
			
			var caption = $('.titleUpload').val();
			var content = $('.descUpload').val();
			uploadlabel = 0;
			
			if(!caption) return false;
			if(!content) return false;
			
			
			 var fd = new FormData($("#postform")[0]);
				//fd.append("CustomField", "This is some extra data"); 
				$.ajax({
					url: basedomain+"post",
					beforeSend : function() {
						// $(".stickypaths").after(uploadingtimelineView(locale.post.uploading));
						$("#timelinepagesCont").prepend(uploadingtimelineView(locale.post.uploading)).masonry('reloadItems');
							var $container = $("#timelinepagesCont"); 
							$container.masonry({
							columnWidth: 1,
							itemSelector: '.box'
							});

						 $('.imagesvertion').val('');
						 $('.titleUpload').val('');
						 $('.descUpload').val('');
					},
					type: "POST",
					data: fd,
					dataType : "JSON",
					processData: false,  // tell jQuery not to process the data
					contentType: false,   // tell jQuery not to set contentType
					error: function() {
						$(".loadingBar").width('100%');   
						w = 0;
						clearTimeout(timeouts);
						setTimeout(function(){$(".loadingBar").width('0%'); },1000);
					}
					}).done(function( data ) {
						var html = "";
						$('.onuploading').remove();
						$(".loadingBar").width('100%');  
						w = 0;
						clearTimeout(timeouts);
						setTimeout(function(){$(".loadingBar").width('0%'); },1000);
						if(data.result){
							html = timelineView(data.data);
						}else{
							html = locale.post.failed;
						}
						
						// $(".stickypaths").after(html);
												
						$("#timelinepagesCont").prepend(html).masonry('reloadItems');
							var $container = $("#timelinepagesCont"); 
							$container.masonry({
							columnWidth: 1,
							itemSelector: '.box'
							});
				});

			
			timeouts = setTimeout(checkProgress(),10);
		}else {
			// $(".uploadlabel").html(locale.uploadlabel.upload); 
			uploadlabel = 1;
		}
		
	});	
	
	$(document).on('click','.openuploadvertion',function(){
	
		$('.imagesvertion').trigger('click');
		
	});
	
	$(document).on('change','.imagesvertion',function(){
		 
		//tempimagespreview();
		readURL(this,".previewimages")
	});
	
	function readURL(input,target) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				var html = "<img src='"+e.target.result+"' class='maxWidth100 maxHeight100' />";
				$(target).html(html);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
 
	function tempimagespreview(ultm){
		
		if(uploadlabel==1){
			$(".previewimages").html(locale.uploadlabel.post); 
			
			 var fd = new FormData($("#postform")[0]);
				//fd.append("CustomField", "This is some extra data"); 
				$.ajax({
					url: basedomain+"tempimages",
					beforeSend : function() {
						$(".previewimages").html(uploadingtimelineView(locale.post.uploading)); 
						
					},
					type: "POST",
					data: fd,
					dataType : "JSON",
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
					}).done(function( data ) {
						var html = ""; 
						if(data.result){
							html = "<img src='"+data.data.imagesdata.image_full_path+"' />";
						}else{
							html = locale.post.failed;
						}
						
						$(".previewimages").html(html);
				});
 
				uploadlabel = 0;
		}else {
			$(".previewimages").html(locale.uploadlabel.upload); 
			uploadlabel = 1;
		}
		
	}
	
	$(document).on('click','.ads-block',function(){
		$(".ads-block").removeClass('active');
		$(this).addClass('active');
		var item = $(this).attr('ads-block');
		if(item=='item1') window.location = location.href;
		else {
			$('.ads-block-timeline').removeClass('item2');
			$('.ads-block-timeline').removeClass('item3');
			$('.ads-block-timeline').addClass(item);			
						
		}
	});
	
	function uploadingtimelineView(message){
		var html ="<div class='ads-block-timeline item3 box v3 onuploading'>";
		html +=message;
		html +="</div>";
		
		return html;
		
	}
	
	function slideLoadPagingView(message){
		var html ="<div class='loadingContentBox slideloadpaging' >";
		html +="<div  class='loadingContentBoxLabel'  >";
		html +=message;
		html +="</div>";
		html +="</div>";
		
		return html;
		
	}
	function timelineView(e){
				
				var items = 3;
				var vitems = 3;
				if(e.imagesdata.image_type=='L'){ items = 2; vitems=2 }
				if(e.imagesdata.image_type=='P'){ items = 4; vitems=2 }
				if(e.imagesdata.image_type=='P5'){ items = 5; vitems=4 }
				if(e.imagesdata.image_type=='B')items = 3;
				 ecoolme =0;
				if(e.cool.me==true) ecoolme =1; 
				
				var html ="";
				html+="<div class='ads-block-timeline item"+items+" box v"+vitems+"' style='margin:5px' >";
				html+="                   <div class='itemText'>";
				html+="                       <div class='left'><a href='#' class='CatTag'>";
				html+=								e.category_name;
				html+="                            </a></div>";
				html+="                        <div class='right'>";
				html+="                        <span>";
				if(ecoolme){
					html+="                            <i class='icon-thumbs-up addcool' vsid='"+e.vsid+"' cid='"+e.id+"' ct='"+e.cool.total+"' >&nbsp;<span class='vcool"+e.id+" top0 posRel left0 '  coolme='"+ecoolme+"' >"+e.cool.total+"</span> </i>";
				}else{
						html+="                            <i class='icon-thumbs-down addcool' vsid='"+e.vsid+"' cid='"+e.id+"' ct='"+e.cool.total+"' >&nbsp;<span class='vcool"+e.id+" top0 posRel left0'  coolme='"+ecoolme+"' >"+e.cool.total+"</span> </i>";
				}
				html+="                           <i class='icon-comment-1'>&nbsp; "+e.comment.total+"</i>";
				html+="                        </span>";
				html+="                        </div>";
				html+="                       <div class='clearit'></div>";
				html+="                      <a href='"+basedomain+"post/detail/?g=layer&vsid="+e.vsid+"'  class='titleBlock'>"; 
				
				if(items==3)html+=								 e.caption ; 
				else html+=								 e.caption ; 
				
				html+="						</a>";
				html+="                   </div>";
				html+="                   <div class='itemImg'>";
				html+="                        <img src='"+e.imagesdata.image_full_path+"'>";
				html+="                   </div>";
				html+="               </div>";
				
			return html;
	}
	
	
	$(document).on('keypress','.searchPost',function (e) {
		 
		  if (e.which == 13) {
				window.location = basedomain + isactivepages + "?s=" + $(this).val();
		  }
	});
	
	 var pages = 0;
	 var loadslideok = 0;
	function callPaging(func,selector,limit,incpages){
  
			var func = func; 
			var selector = selector;  
			var limit = limit;  
			if(pages==0) pages = 2;
			if(limit==0) limit = 5;
			var start = 0;
			if(pages>1)start = (pages-1)*limit;
			else return false;
 
			if(incpages)pages++;
			
			// find object
			var stringfunc = window[func];

			// is object a function?
			if (typeof stringfunc === "function") stringfunc.apply(null, [ selector, start ] );
			
			
	}
	
	function timelinePaging(selector,start){
		
				var s = $(".searchPost").val();
				 if(loadslideok==1) return false;
				 if(loadslideok==3) return false;
				$.ajax({
					url: basedomain+"post/pages",
					beforeSend : function() {
						$(selector).append(slideLoadPagingView(locale.post.uploading));
						
						
						loadslideok=1;
					},
					type: "POST",
					data: {start:start,s:s},
					dataType : "json",
					error: function() {
						$(".loadingBar").width('100%'); 
						saveprofileok=0; 
						w = 0;
						clearTimeout(timeouts);
						setTimeout(function(){$(".loadingBar").width('0%'); },1000);
					}
					}).done(function( data ) {
						var html ="";
						$('.slideloadpaging').remove();
						$(".loadingBar").width('100%');  
						w = 0;
						clearTimeout(timeouts);
						setTimeout(function(){$(".loadingBar").width('0%'); },1000);
						if(data.result){
							$.each(data.data,function(i,e){
									html += timelineView(e);
							});  
							loadslideok=2;
							pages++;
						}else{
							html = slideLoadPagingView('end of post history');
							loadslideok=3;
						}
						
						//$(selector).append(html);
					
						$("#timelinepagesCont").append(html).masonry('reloadItems');
							var $container = $("#timelinepagesCont"); 
							$container.masonry({
							columnWidth: 1,
							itemSelector: '.box'
							});
					});
			timeouts = setTimeout(checkProgress(),10);
	}
	 
	var loadcommentok = 0;
  
	$(document).on('keypress','.addSocialComment',function (e) {
		
		if(e.which==13&&e.shiftKey)	{
			 
		}else {
			if (e.which == 13) {
		  		var thisobject = $(this);
		  		var cmt = $(this).val();
		  		
		  		var vsid = $(this).attr('vsid');
				 if(loadcommentok==1) return false; 
				 if(cmt=='') return false; 
		  
				$.ajax({
					url: basedomain+"post/comment",
					beforeSend : function() {
						thisobject.val(''); 
						thisobject.attr('placeholder',locale.post.uploading); 
						thisobject.attr('disabled',true);
						loadcommentok=1;
					},
					type: "POST",
					data: {message:cmt,vsid:vsid},
					dataType : "json"
					}).done(function( data ) {
						var html ="";
						$('.onuploading').remove();
					
						if(data.result){
 
							html  = commentView(data.data); 
							$(".commentBox").prepend(html);
							thisobject.attr('placeholder','Add Comment ...'); 
							 
						}else{
							thisobject.attr('placeholder','failed to save comment'); 
							 
						}
						thisobject.attr('disabled',false);
						loadcommentok=0;  
						
					});
					
			}
		 }
	});
	
	
	function commentView(e){
		  var html ="";
				html+="<div class='listComment clearfix'>";
              	html+="<div class='imgProfCom left'><img src='"+e.img+"'  class='imgProfCom' /></div>";
             	html+="<div class='boxNameCom left'>";
				html+="<a href='#'>  "+e.fullname+"</a>";
				html+="<h6>&nbsp;  "+e.createddate+"</h6>";
            	html+="<p>  "+e.comment+"</p>";
            	html+="</div>";
            	html+="</div>";
			return html;
	}
	
	
	function commentPaging(selector,start){
				 
		  		 
		  		var vsid = $(selector).attr('vsid');
				 if(loadcommentok==1) return false; 
				if(loadcommentok==1) return false;
				 if(loadcommentok==3) return false;
				 
				$.ajax({
					url: basedomain+"post/pagecomment",
					beforeSend : function() {
						$(selector).append(slideLoadPagingView(locale.post.uploading));
						loadcommentok=1;
					},
					type: "POST",
					data: {start:start,vsid:vsid},
					dataType : "json"
					}).done(function( data ) {
							var html ="";
							$('.slideloadpaging').remove();
							if(data.result){
								$.each(data.data,function(i,e){
										html +=  commentView(e);
								});  
								loadcommentok=2;
								pages++;
							}else{
								html = slideLoadPagingView('end of comment history');
								loadcommentok=3;
							}
							
							$(selector).append(html); 
					});
	}
	
	
	function commentPagingonShare(selector,start){
				 
		  		 
		  		var vsid = $(selector).attr('vsid');
				 if(loadcommentok==1) return false; 
				if(loadcommentok==1) return false;
				 if(loadcommentok==3) return false;
				 
				$.ajax({
					url: basedomain+"share/pagecomment",
					beforeSend : function() {
						$(selector).append(slideLoadPagingView(locale.post.uploading));
						loadcommentok=1;
					},
					type: "POST",
					data: {start:start,vsid:vsid},
					dataType : "json"
					}).done(function( data ) {
							var html ="";
							$('.slideloadpaging').remove();
							if(data.result){
								$.each(data.data,function(i,e){
										html +=  commentView(e);
								});  
								loadcommentok=2;
								pages++;
							}else{
								html = slideLoadPagingView('end of comment history');
								loadcommentok=3;
							}
							
							$(selector).append(html); 
					});
	}
	var loadcoolsok = 0;
	$(document).on('click','.addcool',function(){
			var thisobj = $(this);
			var vsid = $(this).attr('vsid');
			var cid = $(this).attr('cid');
			var coolme = parseInt($(".vcool"+cid).attr('coolme'));
			var ct = parseInt($(this).attr('ct'),10); 
		    
			if(loadcoolsok==1) return false; 
		  
			$.ajax({
					url: basedomain+"post/cool",
					beforeSend : function() {
						  
						$(".vcool"+cid).html(locale.post.uploading); 
						 
						loadcoolsok=1;
					},
					type: "POST",
					data: {vsid:vsid},
					dataType : "json"
					}).done(function( data ) {
						var html =""; 
						if(data.result){
 
							if(!coolme) {
								ct++;
								thisobj.removeClass('icon-thumbs-down');
								thisobj.addClass('icon-thumbs-up');
								$(".vcool"+cid).attr('coolme',1);
							}else {
								ct--;
								thisobj.removeClass('icon-thumbs-up');
								thisobj.addClass('icon-thumbs-down');
								$(".vcool"+cid).attr('coolme',0);
							}
							
							 
							$(".vcool"+cid).html(ct); 
							thisobj.attr('ct',ct)
						}else{
							$(".vcool"+cid).html(ct); 
							 
						}
						 
						loadcoolsok=0;   
					});
					
	})
	
	 
	 $(document).on('click','.pluscommunitybutton',function(){
			 
			window.location = basedomain+"setting/community";
	 })
	
	function newcommunityinlistview(e){
		var html = "<li class='clearfix'>  <div class='categoriesTitle lefte'> "; 
		html+="  <a href='"+basedomain+e.communityalias+"' >"+e.communityname+"</a> ";
		html+=" </div></li>";
		return html;
	}
	
	
	var uploadlabelcommunity = 0; 
	$(document).on('click','.addnewcommunity',function(){
			
	
		if(uploadlabelcommunity==1){
			 
			var cmtnm = $('.cmtnm').val();
		 
			uploadlabelcommunity = 0;
			
			if(!cmtnm) return false;
			 
			
			
			 var fd = new FormData($("#newcommunityform")[0]);
				//fd.append("CustomField", "This is some extra data"); 
				$.ajax({
					url: basedomain+"community/addnewcommunity",
					beforeSend : function() {
					 
						$(".addnewcommunity").after("<span class='onuploading' >"+locale.post.uploading+"</span>");
						 
						 $('.cmtnm').val('');
						 $('.imglogo').val('');
						  
					},
					type: "POST",
					data: fd,
					dataType : "JSON",
					processData: false,  // tell jQuery not to process the data
					contentType: false   // tell jQuery not to set contentType
					}).done(function( data ) {
						var html = "";
						$('.onuploading').remove();
						if(data.result){
							html = newcommunityinlistview(data.data);
							$(".communitylist").prepend(html);
						} 
						  
				});

			
			
		}else {
		 	
			uploadlabelcommunity = 1;
		}
		
	});	
	
	var saveprofileok = 0; 

	$(document).on('click','.saveMyProfile',function(){ 
			// var fullname = $(".fullnameTxt").val(); 
			// var aboutme = $(".aboutmeTxt").val(); 
			// var arrDob = [ $(".dob_y").val() , $(".dob_m").val() ,$(".dob_d").val() ]; 
			// var dob = arrDob.join('');
			// var gennder = $(".gennderTxt").val(); 
		    
			if(saveprofileok==1) return false; 
			 var fd = new FormData($("#updateProfile")[0]);
			
			  $.ajax({
					url: basedomain+"profile/update", 
					type: "POST",
					beforeSend : function() { 
						saveprofileok=1;   
					},
					data:fd,
					dataType : "json",
					processData: false,  // tell jQuery not to process the data
					contentType: false ,  // tell jQuery not to set contentType
					error: function() {
						$(".loadingBar").width('100%'); 
						saveprofileok=0; 
						w = 0;
						clearTimeout(timeouts);
						setTimeout(function(){$(".loadingBar").width('0%'); },1000);
					}
			}).done(function( data ) {
					$(".loadingBar").width('100%'); 
					saveprofileok=0;   
					w = 0;
					clearTimeout(timeouts); 
					setTimeout(function(){$(".loadingBar").width('0%'); },1000);
			}) ;
			
			timeouts = setTimeout(checkProgress(),10);
		 
		});
		
		function checkProgress() { 
			$(".loadingBar").width(w+'%'); 
			// console.log(w);
			w++; 
			timeouts = setTimeout(function(){checkProgress()},10);
		}