<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\InfusionsoftHelper;
use App\Module;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        // Wouldn't normaly do this, but for brevity's sake

        $courses = Module::available();

        return view('auth.register', compact('courses'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'is_admin' => 'boolean',
            'courses.*' => 'string|exists:modules,course_key',
            'password' => 'required|string|min:6|confirmed',
        ]);

        return $validator;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $data = $request->all();
        
        $this->validator($data)->validate();
        
        $data['is_admin'] = isset($data['is_admin']) ? (int)$data['is_admin'] : 0;
        $data['courses'] = isset($data['courses']) ? $data['courses'] : [];

        if (!$data['is_admin']) {
            if (empty($data['courses'])) {
                return redirect()->back()->withErrors([
                    'courses' => ['Can\'t create a contact without selecting a course.']]);
            } else {
                $contact = (new InfusionsoftHelper)->createContact([
                    'Email' => $data['email'],
                    '_Products' => implode(',', $data['courses'])
                ]);

                if (!$contact) {
                    return redirect()->back()->withErrors([
                        'email' => ['Can\'t create a contact with these details.']]);
                }
            }
        }

        event(new Registered($user = $this->create($data)));

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $data['is_admin'],
            'password' => Hash::make($data['password']),
        ]);
               
    }
}
