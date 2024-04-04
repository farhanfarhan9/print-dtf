<div>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Upload Customer
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex sm:justify-between">
            {{-- <input type="file" label="Import Customer Data" class="w-1/3 mt-2 mx-2 sm:w-1/6 sm:mt-0" blue icon="upload" wire:model="file"> --}}
            <input type="file" wire:model="file">
            <x-button wire:click="save" :disabled="empty($data)" class="w-1/3 mt-2 sm:w-1/6 sm:mt-0 {{ empty($data) ? 'opacity-90 cursor-not-allowed' : '' }}" label="Save Customer" green icon="save">
            </x-button>
        </div>
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
            <div class="text-center text-red-500">
                @if ($typeerror)
                    {{ $typeerror }}
                @endif
            </div>
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Phone
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Alamat
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($data)
                    @foreach ($data as $key => $value)
                        @if ($key < 4)
                            @continue
                        @endif
                        <tr
                            class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $key }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $value[1] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $value[8] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $value[3] }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="y-4 text-center" colspan="4">No Preview Data</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
