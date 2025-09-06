@php
use App\Helpers\AppSettingHelper;
@endphp
<meta name="description" content="{{ AppSettingHelper::get('appHeadline') }}">
<meta name="author" content="Doxadigital">
<title>{{ AppSettingHelper::get('appName') }} </title>
<meta name="robots" content="noindex, nofollow" />