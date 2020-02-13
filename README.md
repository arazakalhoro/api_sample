# Rest API sample

In this API I have used Codeigniter standalone [Active Records](https://codeigniter.com/userguide2/database/active_record.html) class for database queries and [GUMP](https://github.com/Wixel/GUMP) for validation.

## Installation
You have set place API into your server root directory and configure the database in ```./includes/config.php```


## Usage
```HTTP
GET: http://YOUR_WEBSITE_URL/api/index
```

```php
public function index(){
	$response = [
		'message' => 'Welcome to my api',
		'error'   => false,
		'data'    => []
	];
	$this->response($response, 200);
}
```
For query database i.e get all users 
```HTTP
GET: http://YOUR_WEBSITE_URL/api/get_users
```
```php
public function get_users(){
    $users = $this->db->get('users')->result();
    $response = [
        'message' => 'Welcome to my api',
        'error'   => false,
        'data'    => $users
    ];
    $this->response($response, 200);
}
```
For $_POST params validation i.e user login
```HTTP
POST: http://YOUR_WEBSITE_URL/api/user_login
```
```php
public function get_users(){
    // You don't have to sanitize, but it's safest to do so.
    $_POST = $this->form_validation->sanitize($_POST); 

	$this->form_validation->validation_rules(
		array(
			'email'         =>  'required|valid_email|max_len,120',
			'password'      =>  'required|min_len,6'
			)
	);

	$user_data = $this->form_validation->run($_POST,true);
	if( $user_data === false) {
	    $response = [
	        'message' => 'Form Validation error',
	        'error'   => true,
	        'data'    => $this->form_validation->get_errors_array()
	    ];
		$this->response($response, 403);
	}
	//if data is valid then Good to Go
}
```
