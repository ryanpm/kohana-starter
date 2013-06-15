<script>

    function checkfields(e){  
            // ignore tab key
            if( e.keyCode == 9 )return;
            var error = false;
            
            var id = $(this).attr('id');

            $('#'+id+'_error').remove(); 
            if( $.trim( $(this).val() ) == '' ){ 
                if( e.type == 'blur' ){
                    display_error(this,$(this).attr('placeholder')+' is required');
                } 
                error = true;
            }else{ 
                
                console.log(id); 
                if( id == 'confirmpassword' || id == 'password' ){
                    if( id == 'password' && e.type == 'keyup' ){
                        $('#confirmpassword').val('').css('background','white').attr('isvalid','false');  
                    } 
                    $('#confirmpassword_error').remove();  
                    if( $.trim( $('#password').val() ) != '' && $.trim( $('#confirmpassword').val() ) != '' ){
                        if( $.trim( $('#password').val() ) != $.trim( $('#confirmpassword').val() ) ){
                            if(  e.type == 'blur' ){
                                display_error($('#confirmpassword'),'Confirm password does not matched');
                            }
                            error = true;
                        }
                    }
                    
                }   
            }  
            
            if(error){
                $(this).attr('isvalid','false');
            }else{
                $(this).css('background','lightgreen').attr('isvalid','true'); 
            }
             
            checkSubmitButton();
              
            
    }
    
</script>
<form id="form_registration"  method="post" class="form" action="<?= URL::site('register/submit') ?>">
<div>
    <div class="header">Registration</div>
    <div id="server_error" class="error_message" style="margin-bottom: 10px;"></div>
    <input type="text" id="firtname" name="firstname" placeholder="First Name" class="field" />
    <input type="text" id="lastname" name="lastname" placeholder="Last Name"  class="field" />
    <input type="text" id="username" name="username" placeholder="Username"  class="field" />
    <input type="password" id="password" name="password" placeholder="Password"  class="field" />
    <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password"  class="field" />
    <input type="submit" id="submit" value="Submit" /><br /><br />
    <a href="<?= URL::site('login') ?>">Login</a>
</div>
</form>
