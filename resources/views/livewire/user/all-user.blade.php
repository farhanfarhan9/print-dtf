<div>
    @if (session('userCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('userCreated')[0] }}',
                    description: '{{ session('userCreated')[1] }}',
                    icon: '{{ session('userCreated')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('userEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{ session('userEdited')[0] }}',
                    description: '{{ session('userEdited')[1] }}',
                    icon: '{{ session('userEdited')[2] }}',
                    timeout: 3000
                })
            })
        </script>
    @endif
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Data User
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex sm:justify-between">
            <x-input wire:model.live.debounce.300ms="search" icon="search" class="sm:!w-1/4" shadowless="true"
                placeholder="Cari User" />
            <x-button label="Tambah User" href="{{ route('user.create') }}" class="w-1/3 mt-2 sm:w-1/6 sm:mt-0" green
                icon="plus" />
        </div>
        <a href="{{ route('user.archieve') }}" class="text-slate-600 hover:underline">Data Arsip</a>
        <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
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
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                        <tr wire:key="{{ $user->id }}"
                            class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $key + 1 }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->roles == 'admin')
                                    <div class="inline-block px-4 py-1 text-white bg-green-500 rounded-xl">
                                        {{ $user->roles }}</div>
                                @else
                                    <div class="inline-block px-4 py-1 text-white bg-orange-500 rounded-xl">
                                        @if ($user->roles == 'operator')
                                            Operator
                                        @elseif($user->roles == 'operator_sublim')
                                            Operator Sublim
                                        @elseif($user->roles == 'operator_dtfuv')
                                            Operator DTF UV
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-5">
                                    <x-button href="{{ route('user.edit', $user->id) }}" label="Edit" primary />
                                    <x-button wire:click="deleteDialog({{ $user->id }})" label="Hapus" red />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center">Data Kosong</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            {{-- <x-modal.card title="{{ isset($editedUser) ? $editedUser->name : '' }}" blur wire:model.defer="depositModal">
                <x-input type="number" class="!pl-[2.5rem]" label="Jumlah deposit" prefix="Rp."
                    wire:model="machineNo" />

                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <div class="flex">
                            <x-button flat label="Cancel" x-on:click="close" />
                            <x-button primary label="Simpan" wire:click="addDeposit" />
                        </div>
                    </div>
                </x-slot>
            </x-modal.card> --}}
        </div>
        <div class="mt-2">
            {{ $users->links() }}
        </div>
    </div>
</div>
