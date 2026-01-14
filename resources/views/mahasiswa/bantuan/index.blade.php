@extends('layouts.mahasiswa')

@section('title', 'Pusat Bantuan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Pusat Bantuan Mahasiswa</h1>
                <p class="text-slate-500 mt-2">Temukan jawaban atas masalah Anda atau hubungi kami langsung.</p>
            </div>
            <a href="{{ request('return_url', '/mahasiswa/pembayaran') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
        </div>
        
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Contact Options -->
            <div class="space-y-6">
                <h3 class="font-bold text-lg text-slate-800">Hubungi Kami</h3>
                
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-green-500 hover:bg-green-50 transition group">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700">WhatsApp Helpdesk</h4>
                        <p class="text-sm text-slate-500">Respon Cepat (08:00 - 16:00)</p>
                    </div>
                </a>

                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=2472022@maranatha.ac.id" target="_blank" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition group">
                     <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700">Email Support</h4>
                        <p class="text-sm text-slate-500">helpdesk@edutrack.ac.id</p>
                    </div>
                </a>
            </div>

            <!-- FAQ -->
            <div>
                <h3 class="font-bold text-lg text-slate-800 mb-4">Pertanyaan Umum (FAQ)</h3>
                <div class="space-y-4">
                    <details class="group bg-slate-50 rounded-xl border border-slate-200">
                        <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4">
                            <span>Bagaimana cara membayar tagihan?</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="text-slate-600 px-4 pb-4 text-sm">
                            Anda dapat melakukan pembayaran melalui BCA Virtual Account menggunakan m-BCA, KlikBCA atau ATM BCA. Panduan lengkap tersedia di menu Pembayaran.
                        </div>
                    </details>
                    
                    <details class="group bg-slate-50 rounded-xl border border-slate-200">
                        <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4">
                            <span>Berapa lama verifikasi pembayaran?</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="text-slate-600 px-4 pb-4 text-sm">
                            Pembayaran dengan Virtual Account BCA akan diverifikasi secara otomatis oleh sistem dalam hitungan detik setelah transaksi berhasil.
                        </div>
                    </details>
                    
                    <details class="group bg-slate-50 rounded-xl border border-slate-200">
                        <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4">
                            <span>Saya sudah bayar tapi status belum lunas?</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>
                        <div class="text-slate-600 px-4 pb-4 text-sm">
                            Jika dalam 15 menit status belum berubah, simpan bukti pembayaran Anda dan segera hubungi Helpdesk kami melalui WhatsApp.
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
