Intranet.controller('RegistrationController', ['$scope', '$http', '$modal', function($scope, $http, $modal){
	console.log('RegistrationController was loaded!');
	
	var current_fs, next_fs, previous_fs; //fieldsets
	var left, opacity, scale; //fieldset properties which we will animate
	var animating; //flag to prevent quick multi-click glitches
	var fieldsetn = 1;
	
	$scope.regData = {
			name: '',
			surname: '',
			email: '',
			username: '',
			password: '',
			password2: '',
			role: 'dev',
			country: userCountry
	};
	
	$scope.redirect = '#';
	
	$scope.emptyName = false;
	$scope.emptySurname = false;
	$scope.emptyEmail = false;
	$scope.emptyUsername = false;
	$scope.emptyPassword = false;
	$scope.emptyPassword2 = false;	
	
	$scope.errorPassword = false;
	$scope.errorPasswordMessage = '';
	
	$scope.errorEmail = false;
	$scope.errorEmailMessage = '';
	
	$scope.errorUsername = false;
	$scope.errorUsernameMessage = '';
	
	$scope.urlsRegisterAction = JSON_URLS.registrationAction;
	$scope.urlsCheckUsername = JSON_URLS.checkUsername;
	$scope.urlsCheckEmail = JSON_URLS.checkEmail;
	
	String.prototype.isEmpty = function() {
	    return (this.length === 0 || this.trim() === '');
	};
	
	function displayErrors()
	{	
		if (($scope.regData.name == undefined) || (($scope.regData.name != undefined) && ($scope.regData.name.isEmpty())))
		{
			$scope.emptyName = true;
		} else
			$scope.emptyName = false;
		
		if (($scope.regData.surname == undefined) || (($scope.regData.surname != undefined) && ($scope.regData.surname.isEmpty())))
		{
			$scope.emptySurname = true;
		}else
			$scope.emptySurname = false;
		
		if (($scope.regData.email == undefined) || (($scope.regData.email != undefined) && ($scope.regData.email.isEmpty())))
		{
			$scope.emptyEmail = true;
		}else
			$scope.emptyEmail = false;
		
		if (($scope.regData.username == undefined) || (($scope.regData.username != undefined) && ($scope.regData.username.isEmpty())))
		{
			$scope.emptyUsername = true;
		}else
			$scope.emptyUsername = false;
		
		if (($scope.regData.password == undefined) || (($scope.regData.password != undefined) && ($scope.regData.password.isEmpty())))
		{
			$scope.emptyPassword = true;
		}else
			$scope.emptyPassword = false;
		
		if (($scope.regData.password2 == undefined) || (($scope.regData.password2 != undefined) && ($scope.regData.password2.isEmpty())))
		{
			$scope.emptyPassword2 = true;
		}else
			$scope.emptyPassword2 = false;
		
		
		if ((!$scope.emptyPassword && !$scope.emptyPassword2) && ($scope.regData.password !== $scope.regData.password2))
		{
			$scope.errorPassword = true;
			$scope.errorPasswordMessage = 'Please fill in right password!';
		}else
		{
			$scope.errorPassword = false;
			$scope.errorPasswordMessage = '';
		}
		
		if (($scope.emptyName) || ($scope.emptySurname) || ($scope.emptyEmail) || ($scope.emptyUsername) || ($scope.emptyPassword) || ($scope.emptyPassword2) || ($scope.errorEmail) || ($scope.errorUsername) || ($scope.errorEmpty) || ($scope.errorPassword))
		{
			$scope.$apply();
			return true;
		}
		else
		{
			$scope.$apply();
			return false;
		}
	}
	
	$scope.checkUsername = function(){
		if (($scope.regData.username == undefined) || ($scope.regData.username.isEmpty())) 
		{
			$scope.errorUsername = false;
			$scope.errorUsernameMessage = '';
			return false;
		}
			
		$http({
				method: "POST", 
				url: $scope.urlsCheckUsername,
				data: {'username': $scope.regData.username}
			  })
		.success(function(response){
			if (response.result)
			{	
				$scope.errorUsername = false;
				$scope.errorUsernameMessage = '';
			}else
			{
				$scope.errorUsername = true;
				$scope.errorUsernameMessage = 'Username is already exist!';
			}
			displayErrors();
		});
			
	};
	
	$scope.checkEmail = function(){
		if (($scope.regData.email == undefined) || ($scope.regData.email.isEmpty())) 
		{
			$scope.errorEmail = false;
			$scope.errorEmailMessage = '';
			return false;
		}
			
		
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		var checkResult = emailReg.test($scope.regData.email);
		
		if (!checkResult)
		{
			$scope.errorEmail = true;
			$scope.errorEmailMessage = 'Please enter right email!';
			displayErrors();
			return false;
		}
		
		$http({
				method: "POST", 
				url: $scope.urlsCheckEmail,
				data: {'email': $scope.regData.email}
			  })
		.success(function(response){
			if (response.result)
			{
				$scope.errorEmail = false;
				$scope.errorEmailMessage = '';
			}else
			{
				$scope.errorEmail = true;
				$scope.errorEmailMessage = 'Email is already exist!';
			}
			displayErrors();
		})
	};
	
	$scope.register = function(event)
	{
		setTimeout(function(){
			if (displayErrors()) return false;
			$http({
				method: "POST", 
				url: $scope.urlsRegisterAction,
				data: $scope.regData
			})
			.success(function(response){
				if (response.result) {
				   $scope.redirect = response.redirect;
				   $scope.next(event);
				}
			})
			
			
		}, 500);
	};
	
	
	$scope.next = function(event)
	{
		if(animating) return false;
		animating = true;
		fieldsetn = fieldsetn +1;
		current_fs = $(event.currentTarget).parent();
		next_fs = $(event.currentTarget).parent().next();
		
		//activate next step on progressbar using the index of next_fs
		$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
		//show the next fieldset
		next_fs.show();
		current_fs.animate({opacity: 0}, {
		  	step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale current_fs down to 80%
		  		scale = 1 - (1 - now) * 0.2;
				//2. bring next_fs from the right(50%)
			  	left = (now * 50)+"%";
			  	//3. increase opacity of next_fs to 1 as it moves in
				  opacity = 1 - now;
			  	current_fs.css({'transform': 'scale('+scale+')'});
			  	next_fs.css({'left': left, 'opacity': opacity});
			  }, 
			  duration: 800, 
			  complete: function(){
				  current_fs.hide();
				  animating = false;
			  }, 
			  //this comes from the custom easing plugin
			  easing: 'easeInOutBack'
		  });
	};
	
	$scope.previous = function(event)
	{
		if(animating) return false;
		animating = true;
	    fieldsetn = fieldsetn - 1;
		
		current_fs = $(event.currentTarget).parent();
		previous_fs = $(event.currentTarget).parent().prev();
		
		//de-activate current step on progressbar
		$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
		
		//show the previous fieldset
		previous_fs.show(); 
		//hide the current fieldset with style
		current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. scale previous_fs from 80% to 100%
				scale = 0.8 + (1 - now) * 0.2;
				//2. take current_fs to the right(50%) - from 0%
				left = ((1-now) * 50)+"%";
				//3. increase opacity of previous_fs to 1 as it moves in
				opacity = 1 - now;
				current_fs.css({'left': left});
				previous_fs.css({'transform': 'scale('+scale+')', 'opacity': opacity});
			}, 
			duration: 800, 
			complete: function(){
				current_fs.hide();
				animating = false;
			}, 
			//this comes from the custom easing plugin
			easing: 'easeInOutBack'
		});
	};
	
	$scope.redirectAction = function(){
		window.location = $scope.redirect;
	}
	
}]);

function showRecaptcha(element) {
           Recaptcha.create("6LdvEO8SAAAAACHjXu1Z6D2HIF9OcqMPW2yw8KOf", element, {
             theme: "clean",
             lang: "pt",
             callback: Recaptcha.focus_response_field});
         }


//jQuery time
var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches
var fieldsetn = 1;
var emailantigo;
var passval;

$(".submit").click(function(){
  var captchadesafio = Recaptcha.get_challenge();
  var captcharesposta= Recaptcha.get_response();
 
 $.ajax({  
            type: "POST",
            url: '/php/verifycaptcha.php',
            data: { 'desafio=': captchadesafio, 'resposta=': captcharesposta }  
        }).done(function( msg ) {
    alert( "Data Saved: " + msg );
  });

});