<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('Setting', [
            'countries' => $this->countryDialCodes(),
        ]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_country_code' => ['nullable', 'string', 'max:8'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'dimensions:min_width=512,min_height=512', 'max:4096'],
        ]);

        $hasPhoneCountry = filled($validated['phone_country_code'] ?? null);
        $hasPhoneNumber = filled($validated['phone_number'] ?? null);

        if ($hasPhoneCountry !== $hasPhoneNumber) {
            return back()
                ->withErrors(['phone_number' => 'Kode negara dan nomor telepon harus diisi bersamaan.'])
                ->withInput()
                ->with('active_tab', 'account');
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $uploadedFile = $request->file('profile_photo');
            $imageInfo = getimagesize($uploadedFile->getRealPath());

            if (!$imageInfo) {
                return back()
                    ->withErrors(['profile_photo' => 'File foto tidak valid.'])
                    ->withInput()
                    ->with('active_tab', 'account');
            }

            [$sourceWidth, $sourceHeight] = $imageInfo;
            $imageResource = match ($imageInfo[2]) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($uploadedFile->getRealPath()),
                IMAGETYPE_PNG => imagecreatefrompng($uploadedFile->getRealPath()),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($uploadedFile->getRealPath()) : null,
                default => null,
            };

            if (!$imageResource) {
                return back()
                    ->withErrors(['profile_photo' => 'Format foto tidak didukung.'])
                    ->withInput()
                    ->with('active_tab', 'account');
            }

            $cropSize = min($sourceWidth, $sourceHeight);
            $sourceX = (int) floor(($sourceWidth - $cropSize) / 2);
            $sourceY = (int) floor(($sourceHeight - $cropSize) / 2);
            $targetSize = 1024;
            $canvas = imagecreatetruecolor($targetSize, $targetSize);

            if ($imageInfo[2] === IMAGETYPE_PNG || (function_exists('imageistruecolor') && imageistruecolor($imageResource))) {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }

            imagecopyresampled(
                $canvas,
                $imageResource,
                0,
                0,
                $sourceX,
                $sourceY,
                $targetSize,
                $targetSize,
                $cropSize,
                $cropSize
            );

            $fileName = 'profile-photos/' . uniqid('avatar_', true) . '.png';
            $fullPath = storage_path('app/public/' . $fileName);

            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            imagepng($canvas, $fullPath, 6);

            imagedestroy($canvas);
            imagedestroy($imageResource);

            $validated['profile_photo_path'] = $fileName;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_country_code' => $hasPhoneCountry ? $validated['phone_country_code'] : null,
            'phone_number' => $hasPhoneNumber ? $validated['phone_number'] : null,
        ]);

        if (array_key_exists('profile_photo_path', $validated)) {
            $user->profile_photo_path = $validated['profile_photo_path'];
        }

        $user->save();

        return redirect()->route('setelan')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput()
                ->with('active_tab', 'security');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('setelan')->with('status', 'Password berhasil diperbarui.');
    }

    public function removeProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = null;
        $user->save();

        return redirect()->route('setelan')->with('status', 'Foto profil berhasil dihapus.');
    }

    private function countryDialCodes(): array
    {
        $countries = [
            'Afghanistan' => '+93',
            'Albania' => '+355',
            'Algeria' => '+213',
            'Andorra' => '+376',
            'Angola' => '+244',
            'Argentina' => '+54',
            'Armenia' => '+374',
            'Australia' => '+61',
            'Austria' => '+43',
            'Azerbaijan' => '+994',
            'Bahrain' => '+973',
            'Bangladesh' => '+880',
            'Belarus' => '+375',
            'Belgium' => '+32',
            'Belize' => '+501',
            'Benin' => '+229',
            'Bhutan' => '+975',
            'Bolivia' => '+591',
            'Bosnia and Herzegovina' => '+387',
            'Botswana' => '+267',
            'Brazil' => '+55',
            'Brunei' => '+673',
            'Bulgaria' => '+359',
            'Burkina Faso' => '+226',
            'Burundi' => '+257',
            'Cambodia' => '+855',
            'Cameroon' => '+237',
            'Canada' => '+1',
            'Cape Verde' => '+238',
            'Central African Republic' => '+236',
            'Chad' => '+235',
            'Chile' => '+56',
            'China' => '+86',
            'Colombia' => '+57',
            'Comoros' => '+269',
            'Congo' => '+242',
            'Costa Rica' => '+506',
            'Croatia' => '+385',
            'Cuba' => '+53',
            'Cyprus' => '+357',
            'Czech Republic' => '+420',
            'Denmark' => '+45',
            'Djibouti' => '+253',
            'Dominica' => '+1-767',
            'Dominican Republic' => '+1-809',
            'Ecuador' => '+593',
            'Egypt' => '+20',
            'El Salvador' => '+503',
            'Equatorial Guinea' => '+240',
            'Eritrea' => '+291',
            'Estonia' => '+372',
            'Eswatini' => '+268',
            'Ethiopia' => '+251',
            'Fiji' => '+679',
            'Finland' => '+358',
            'France' => '+33',
            'Gabon' => '+241',
            'Gambia' => '+220',
            'Georgia' => '+995',
            'Germany' => '+49',
            'Ghana' => '+233',
            'Greece' => '+30',
            'Grenada' => '+1-473',
            'Guatemala' => '+502',
            'Guinea' => '+224',
            'Guinea-Bissau' => '+245',
            'Guyana' => '+592',
            'Haiti' => '+509',
            'Honduras' => '+504',
            'Hungary' => '+36',
            'Iceland' => '+354',
            'India' => '+91',
            'Indonesia' => '+62',
            'Iran' => '+98',
            'Iraq' => '+964',
            'Ireland' => '+353',
            'Israel' => '+972',
            'Italy' => '+39',
            'Jamaica' => '+1-876',
            'Japan' => '+81',
            'Jordan' => '+962',
            'Kazakhstan' => '+7',
            'Kenya' => '+254',
            'Kuwait' => '+965',
            'Kyrgyzstan' => '+996',
            'Laos' => '+856',
            'Latvia' => '+371',
            'Lebanon' => '+961',
            'Lesotho' => '+266',
            'Liberia' => '+231',
            'Libya' => '+218',
            'Lithuania' => '+370',
            'Luxembourg' => '+352',
            'Madagascar' => '+261',
            'Malawi' => '+265',
            'Malaysia' => '+60',
            'Maldives' => '+960',
            'Mali' => '+223',
            'Malta' => '+356',
            'Mauritania' => '+222',
            'Mauritius' => '+230',
            'Mexico' => '+52',
            'Moldova' => '+373',
            'Mongolia' => '+976',
            'Montenegro' => '+382',
            'Morocco' => '+212',
            'Mozambique' => '+258',
            'Myanmar' => '+95',
            'Namibia' => '+264',
            'Nepal' => '+977',
            'Netherlands' => '+31',
            'New Zealand' => '+64',
            'Nicaragua' => '+505',
            'Niger' => '+227',
            'Nigeria' => '+234',
            'North Macedonia' => '+389',
            'Norway' => '+47',
            'Oman' => '+968',
            'Pakistan' => '+92',
            'Panama' => '+507',
            'Papua New Guinea' => '+675',
            'Paraguay' => '+595',
            'Peru' => '+51',
            'Philippines' => '+63',
            'Poland' => '+48',
            'Portugal' => '+351',
            'Qatar' => '+974',
            'Romania' => '+40',
            'Russia' => '+7',
            'Rwanda' => '+250',
            'Saudi Arabia' => '+966',
            'Senegal' => '+221',
            'Serbia' => '+381',
            'Seychelles' => '+248',
            'Sierra Leone' => '+232',
            'Singapore' => '+65',
            'Slovakia' => '+421',
            'Slovenia' => '+386',
            'Somalia' => '+252',
            'South Africa' => '+27',
            'South Korea' => '+82',
            'Spain' => '+34',
            'Sri Lanka' => '+94',
            'Sudan' => '+249',
            'Suriname' => '+597',
            'Sweden' => '+46',
            'Switzerland' => '+41',
            'Syria' => '+963',
            'Taiwan' => '+886',
            'Tajikistan' => '+992',
            'Tanzania' => '+255',
            'Thailand' => '+66',
            'Timor-Leste' => '+670',
            'Togo' => '+228',
            'Tonga' => '+676',
            'Trinidad and Tobago' => '+1-868',
            'Tunisia' => '+216',
            'Turkey' => '+90',
            'Turkmenistan' => '+993',
            'Uganda' => '+256',
            'Ukraine' => '+380',
            'United Arab Emirates' => '+971',
            'United Kingdom' => '+44',
            'United States' => '+1',
            'Uruguay' => '+598',
            'Uzbekistan' => '+998',
            'Venezuela' => '+58',
            'Vietnam' => '+84',
            'Yemen' => '+967',
            'Zambia' => '+260',
            'Zimbabwe' => '+263',
        ];

        return collect($countries)
            ->map(fn (string $code, string $country) => [
                'country' => $country,
                'code' => $code,
                'label' => $country . ' (' . $code . ')',
            ])
            ->values()
            ->all();
    }
}