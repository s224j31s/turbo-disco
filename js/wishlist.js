var wish_url = "ajax/ajaxwishlist.php";
var login_url = "login2.php";
var spinner_image = "graphics/spinner.png";
var wish_btn_text = "in your wish list";
$(document).ready(function(){
  //preload spinner image
  let img = document.createElement('img');
  img.setAttribute('src',spinner_image);
  
  //get total number of items in wishlist.php page
  if($('[data-wishlist-item]').length){
    var total_wish = $('[data-wishlist-item]').length;
    }
  else{
    if( parseInt( $('.wish-count').text() ) ){
      var total_wish = parseInt($('.wish-count').text());
    }
    else{
      var total_wish = 0;
    }
  }
  
  //if wishlist empty
  showEmptyNotice(total_wish);
  //delete an item from wishlist
  $('button[name="delete"]').click(function(event){
    event.preventDefault();
    let product_id = $(event.target).data("id");
    let list_id = $(event.target).data("list-id");
    let user_id = $(event.target).data("user-id");
    //create data object and populate with data
    let data_obj = new Object();
    data_obj.id = product_id;
    data_obj.action = 'delete';
    data_obj.listid = list_id;
    data_obj.userid = user_id;
    //make ajax request
    $.ajax({
      beforeSend:addSpinner(event.target),
      type:'post',
      url: wish_url,
      data: data_obj,
      dataType: 'json',
      encode: true 
    })
    .done(function(response){
      if(response.success == true){
        //remove the element that has been clicked
        total_wish=total_wish-1;
        updateWishCount(total_wish);
        let wishlist_item = '[data-wishlist-item="'+product_id+'"]';
        setTimeout(function(){
          $(wishlist_item).remove();
        },1500);
        showEmptyNotice(total_wish);
      }
    });
  });
  
  //Add an item to wishlist
  $('button[name="add"]').click(function(event){
    event.preventDefault();
    let button = event.target;
    let product_id = $(event.target).data("id");
    let list_id = $(event.target).data("list-id");
    let user_id = $(event.target).data("user-id");
    //create data object and populate with data
    let data_obj = new Object();
    data_obj.id = product_id;
    data_obj.action = 'add';
    data_obj.listid = list_id;
    data_obj.userid = user_id;
    //make ajax request
    $.ajax({
      beforeSend:addSpinner(event.target),
      type:'post',
      url: wish_url,
      data: data_obj,
      dataType: 'json',
      encode: true 
    })
    .done(function(response){
      if(response.success == true){
        let button = event.target;
        total_wish=total_wish+1;
        updateWishCount(total_wish);
        //remove spinner after 1.5 seconds
        setTimeout(function(){
          //remove the spinner
          removeSpinner(button);
          //change text inside button
          $(button).text(wish_btn_text);
          //disable button
          $(button).attr('disabled','');
          //add a check mark
          addCheck(button);
        },1500);
      }
      else if(response.success == false && user_id == false){
        //adding item to wishlist has failed (because user is not logged in)
        //remove spinner
        removeSpinner(event.target);
        //take user to login page and send product id to login page via GET
        //get current page and redirect user back to it after login
        let current_page = window.location.href;
        //create the redirect variable and encode as 
        //base64 with btoa() to avoid illegal characters which will break the url
        let redirect_page = '&redirect=' + btoa(current_page);
        let redirect_url = login_url + "?" + "action=add&list=wish&productid=" + product_id + redirect_page;
        //redirect to login page
        //encode the URI to ensure it does not contain unfriendly characters
        window.location.href = encodeURI( redirect_url );
      }
      else{
        removeSpinner(event.target);
      }
    });
  });
  //if the item exists in the wishlist, mark it with the check
  //and disable the add button wishlist items array is created in
  //the head.php section
  ( function() {
    let button = $('button[name="add"]');
    let id = button.attr('data-id');
    let len = wishlist_items.length;
    for( let i=0 ; i < len; i++ ){
      if(wishlist_items[i].product_id == id){
        button.text(wish_btn_text);
        addCheck(button);
        button.attr( 'disabled' , '' );
      }
    }
  }($,wishlist_items));
});

function addSpinner(elm){
  //clone spinner from template on the page
  let sptemplate = $("#spinner-template").html();
  $(elm).append(sptemplate);
}

function removeSpinner(elm){
  //spinner element class is .spinner-container
  let spinner = $(elm).find('.spinner-container');
  spinner.remove();
}

function addCheck(elm){
  let check = $("#check-template").html();
  $(elm).append(check);
}

function updateWishCount(total){
  if(total !== 0){
    $('.wish-count').text(total);
  }
  else{
    $('.wish-count').empty();
  }
}

function showEmptyNotice(total){
  if(total == 0){
    let notice = '<h1 class="text-center no-wish">You are wishless. Make a wish?</h1>';
    $('[data-name="wish-row"]').append(notice);
  }
}