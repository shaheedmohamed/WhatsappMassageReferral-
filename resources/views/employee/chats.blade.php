@extends('layouts.admin')

@section('title', 'الدردشات')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">الدردشات المخصصة</h1>
    <p class="text-gray-600 mt-2">عرض جميع الدردشات من أجهزة مجتمعك</p>
</div>

@if($devices->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <label for="device_select" class="block text-sm font-medium text-gray-700 mb-2">اختر الجهاز</label>
        <select id="device_select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">اختر جهازاً</option>
            @foreach($devices as $device)
            <option value="{{ $device->id }}">{{ $device->name }} - {{ $device->phone_number }}</option>
            @endforeach
        </select>
    </div>
    
    <div id="chats_container" class="mt-6">
        <p class="text-gray-500 text-center py-8">اختر جهازاً لعرض الدردشات</p>
    </div>
</div>

<script>
document.getElementById('device_select').addEventListener('change', function() {
    const deviceId = this.value;
    const container = document.getElementById('chats_container');
    
    if (!deviceId) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">اختر جهازاً لعرض الدردشات</p>';
        return;
    }
    
    container.innerHTML = '<p class="text-gray-500 text-center py-8"><i class="fas fa-spinner fa-spin ml-2"></i>جاري التحميل...</p>';
    
    // Redirect to WhatsApp chats page with selected device
    window.location.href = '{{ route("whatsapp.chats") }}?device_id=' + deviceId;
});
</script>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl ml-3"></i>
        <div>
            <h3 class="font-semibold text-yellow-800">لا توجد أجهزة مخصصة</h3>
            <p class="text-yellow-700 mt-1">لم يتم تخصيص أي أجهزة لمجتمعك بعد. يرجى التواصل مع المدير الفائق.</p>
        </div>
    </div>
</div>
@endif
@endsection
