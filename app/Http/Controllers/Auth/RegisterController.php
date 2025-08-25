<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $states = State::where('country_id', 296)->get();
        return view('auth.register', compact('states'));
    }

    public function getMunicipalities(Request $request)
    {
        $municipalities = Municipality::where('state_id', $request->state_id)->get();
        return response()->json($municipalities);
    }

    public function getParishes(Request $request)
    {
        $parishes = Parish::where('municipality_id', $request->municipality_id)->get();
        return response()->json($parishes);
    }

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
            'state_id' => ['required', 'exists:states,id'],
            'municipality_id' => ['required', 'exists:municipalities,id'],
            'parish_id' => ['required', 'exists:parishes,id'],
        ];

        // if (request('role') === 'seller') {
        //     $rules['company_name'] = ['required', 'string', 'max:255'];
        //     $rules['company_rif'] = ['required', 'string', 'max:20'];
        // }

        return Validator::make($data, $rules);
    }

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
                'state_id' => $data['state_id'],
                'municipality_id' => $data['municipality_id'],
                'parish_id' => $data['parish_id'],
                // 'company_name' => $data['company_name'] ?? null,
                // 'company_rif' => $data['company_rif'] ?? null,
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
        Log::info('Datos recibidos en register', $request->all());
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
