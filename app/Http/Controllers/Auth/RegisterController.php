<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:people'],
            'phone' => ['required', 'string', 'max:20'],
            'identification_type' => ['required', 'string', 'in:V,E,J,G'],
            'identification_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:buyer,seller'],
            'address' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
        ];

        if (request('role') === 'seller') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['company_rif'] = ['required', 'string', 'max:20'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        try {
            DB::beginTransaction();

            $person = Person::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'identification_type' => $data['identification_type'],
                'identification_number' => $data['identification_number'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'address' => $data['address'] ?? null,
                'sector' => $data['sector'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'company_rif' => $data['company_rif'] ?? null,
                'is_active' => true,
                'is_verified' => false,
            ]);

            DB::commit();
            return $person;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        try {
            DB::beginTransaction();
            
            $person = $this->create($request->all());
            event(new Registered($person));
            
            $this->guard()->login($person);
            
            DB::commit();

            if ($response = $this->registered($request, $person)) {
                return $response;
            }

            return $request->wantsJson()
                ? new JsonResponse([], 201)
                : redirect($this->redirectPath());

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar el usuario. Por favor, intente nuevamente.']);
        }
    }

    protected function registered(Request $request, $person)
    {
        if ($person->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }
        return redirect()->route('buyer.dashboard');
    }
}
