<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   	<link rel="stylesheet" href="{{asset('css/new_password.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>L'KARRIERE</title>
    <style>
    	
    </style>
</head>
<body>
    <div id="box">
        <div id="content">
            <div id="content-title">New Password</div>
            <form id="form" action="{{route('new_password')}}" method="POST">
                @csrf
                <!-- behind the scenes -->
                <input type="hidden" value="{{$token}}" name="token"/>
                <input id="form-error-message" type="hidden" value="{{$message}}"/>
                <!-- ||||||||||||||||| -->
                <label class="form-title" for="password">Password :</label>
                <input class="form-input" type="password" required placeholder="/909dng." name="password"/>
                <label class="form-title" for="repeat_password">Repeat password :</label>
                <input class="form-input" type="password" required placeholder="/909dng." name="repeat_password"/>
                <input id="form-button" type="submit" value="Submit">
            </form>
        </div>
    </div>
    <script>
    	var message = document.getElementById('form-error-message').value;
		 
    	if(message === 'password_error') 
        {
    		Swal.fire({
	   			icon: 'error',
	   			title: 'passwords didn\'t match',
                confirmButtonColor: '#461486',
	   		})
    	}else if(message === 'general_error') 
        {
    		Swal.fire({
	   			icon: 'error',
	   			title: 'something went wrong please contact the support',
                confirmButtonColor: '#461486',
	   		})
    	}

    </script>
</body>
</html>