<div>
    <x-slot name="header">
        <div class="space-y-6 ">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Dasboard
            </h2>
            <p class="text-2xl font-semibold">Selamat datang {{Auth::user()->name}}</p>
        </div>
    </x-slot>
    <div class="py-12">
        <div class=" md:flex md:mb-0 md:gap-8">
            <div class="border-2 border-[#70C276] w-full mb-10 md:mb-0 md:w-1/3 h-fit rounded-xl bg-[#EEFCEF] text-gray-800">
                <div class="flex gap-8 py-4 px-7">
                    <div class="p-2 my-auto bg-white border rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#70c276]" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold">{{ $count_user }}</p>
                        <p class="font-medium">Jumlah Customer</p>
                    </div>
                </div>
                <div class="border-t-2 border-[#70C276] px-4 py-2">
                    <a href="{{ route('customer.index') }}" class="flex justify-between">
                        <p class="font-semibold text-[#70C276]">Lihat detail</p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#70c276]" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="border-2 border-[#A69AEA] w-full mb-10 md:mb-0 md:w-1/3 h-fit rounded-xl bg-[#F4F2FF] text-gray-800">
                <div class="flex gap-8 py-4 px-7">
                    <div class="p-2 my-auto bg-white border rounded-xl">
                        <svg class="h-5 w-5 text-[#A69AEA]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 18 21">
                            <path
                                d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold">{{ $count_po }}</p>
                        <p class="font-medium">Jumlah Order</p>
                    </div>
                </div>
                <div class="border-t-2 border-[#A69AEA] px-4 py-2">
                    <a href="{{ route('order.index') }}" class="flex justify-between">
                        <p class="font-semibold text-[#A69AEA]">Lihat detail</p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#A69AEA]" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="border-2 border-[#F39264] w-full mb-10 md:mb-0 md:w-1/3 h-fit rounded-xl bg-[#FFF1EE] text-gray-800">
                <div class="flex gap-8 py-4 px-7">
                    <div class="p-2 my-auto bg-white border rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg"class="h-5 w-5 text-[#F39264]"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold">{{ $count_process }}</p>
                        <p class="font-medium">Jumlah Internal Process Hari Ini ( {{\Carbon\Carbon::today()->format('d F Y') }})</p>
                    </div>
                </div>
                <div class="border-t-2 border-[#F39264] px-4 py-2">
                    <a href="{{ route('internal_process.index') }}" class="flex justify-between">
                        <p class="font-semibold text-[#F39264]">Lihat detail</p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#F39264]" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
