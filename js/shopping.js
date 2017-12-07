var shop_button_text = "in your cart";
$(document).ready(function(){
  let cart_button = $('[name="cart"]');
  let quantity = $('[name="quantity"]');
  $(cart_button).click(function(event){
    event.preventDefault();
    let product_id = cart_button.data('id');
    let product_quantity = quantity.val();
    let shoppingcart_uid = cart_uid;
    let dataobj = new Object();
    dataobj.action = "add";
    dataobj.menu_id = product_id;
    dataobj.quantity = product_quantity;
    dataobj.cart_uid = shoppingcart_uid;
    console.log(dataobj);
     $.ajax({
      beforeSend:addSpinner(event.target),
      type:'post',
      url: 'ajax/ajaxcart.php',
      data: dataobj,
      dataType: 'json',
      encode: true 
    })
    .done(function(response){
      console.log(response);
      if(response.success == true){
        //successful
        window.setTimeout(removeSpinner(event.target),3000);
        //update cart count in navigation
        updateCartCount( response.total );
      }
      else{
        //unsuccessful
        removeSpinner(event.target);
        
      }
    });
  });
  //quantity button
  $('[data-group="quantity"]').click(function(event){
    event.preventDefault();
    let product_id = $(event.target).data('product-id');
    let btn_function = $(event.target).data('function');
    let quantity_selector = 'input[data-id="'+product_id+'"][name="quantity"]';
    console.log(quantity_selector);
    let current_quantity = $(quantity_selector).val();
    console.log(current_quantity);
    if(btn_function == "add"){
      //add the quantity
      current_quantity++;
      $(quantity_selector).val(current_quantity);
    }
    else if(btn_function == "subtract"){
      //subtract the quantity
      current_quantity--;
      if(current_quantity < 0){
        current_quantity = 0;
      }
      $(quantity_selector).val(current_quantity);
    }
  });
});

function updateCartCount( count ){
  $('.cart-count').text( count );
}

