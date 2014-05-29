	var uploadlabel = 0; 
	$(document).on('click','.plusNewHome',function(){
			
	
		if(uploadlabel==1){
			$(".uploadlabel").html(locale.uploadlabel.post);
			
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
						$(".stickypaths").after(uploadingtimelineView(locale.post.uploading));
						
						 $('.imagesvertion').val('');
						 $('.titleUpload').val('');
						 $('.descUpload').val('');
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
							html = timelineView(data.data);
						}else{
							html = locale.post.failed;
						}
						
						$(".stickypaths").after(html);
				});

			
			
		}else {
			$(".uploadlabel").html(locale.uploadlabel.upload);
			
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
		var html ="<div class='ads-block-timeline item3 onuploading'>";
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
				if(e.imagesdata.image_type=='L')items = 2;
				if(e.imagesdata.image_type=='P')items = 3;
				if(e.imagesdata.image_type=='B')items = 3;
							
				var html ="";
				html+="<div class='ads-block-timeline item"+items+"' style='margin:5px' >";
				html+="                   <div class='itemText'>";
				html+="                       <div class='left'><a href='#' class='CatTag'>";
				html+=								e.category_name;
				html+="                            </a></div>";
				html+="                        <div class='right'>";
				html+="                        <span>";
				html+="                            <i class='icon-thumbs-up addcool' vsid='"+e.vsid+"' cid='"+e.id+"' ct='"+e.cool.total+"' >&nbsp;<span class='vcool"+e.id+"' >"+e.cool.total+"</span> </i>";
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
					dataType : "json"
					}).done(function( data ) {
						var html ="";
						$('.slideloadpaging').remove();
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
						
						$(selector).append(html);
						
						
					});
 
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
              	html+="<div class='imgProfCom left'></div>";
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
 
							ct++;
							$(".vcool"+cid).html(ct); 
							 thisobj.attr('ct',ct)
						}else{
							$(".vcool"+cid).html(ct); 
							 
						}
						 
						loadcoolsok=0;  
						
					});
					
	})