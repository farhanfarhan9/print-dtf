<div>
    @if (session('addressCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{session('addressCreated')[0]}}',
                    description: '{{session('addressCreated')[1]}}',
                    icon: '{{session('addressCreated')[2]}}',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('addressEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: '{{session('addressEdited')[0]}}',
                    description: '{{session('addressEdited')[1]}}',
                    icon: '{{session('addressEdited')[2]}}',
                    timeout: 3000
                })
            })
        </script>
    @endif

    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Alamat
            </h2>
            <x-button href="{{ route('address.create') }}" green label="Tambah Alamat Baru" />
        </div>
    </x-slot>
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col space-y-7">
            @forelse ($addresses as $address)
                <x-card shadow='false' class="!px-24 space-y-5" wire:key='{{ $address->id }}'>
                    @if ($address->active == 1)
                        <button type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Alamat
                            Utama</button>
                    @else
                        <div class="flex justify-end">
                            <x-button wire:click='update({{ $address->id }})' rounded positive
                                label="Jadikan Alamat Utama" />
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <div>
                            <p class="text-lg font-bold">Kota/Kecamatan</p>
                            <p>{{ $address->city }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold">Kode Pos</p>
                            <p>{{ $address->postal }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Nomor Hp</p>
                        <p>{{ $address->phone }}</p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-lg font-bold">Alamat</p>
                            <p>{{ $address->address }}</p>
                        </div>
                        <div>
                            <x-button href="{{ route('address.edit', $address->id) }}" label="Edit"
                                primary />
                            <x-button wire:click="deleteDialog({{ $address->id }})" label="Hapus" red />
                        </div>
                    </div>
                </x-card>
            @empty
                <p class="text-2xl text-center">Data alamat kosong</p>
            @endforelse
        </div>

    </div>
    {{-- @dd(session('addressCreated')) --}}
    {{-- @if (session('addressCreated'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: 'Sukses',
                    description: 'Berhasil menambahkan data',
                    icon: 'success',
                    timeout: 3000
                })
            })
        </script>
    @endif --}}
    {{--
    @elseif (session('addressEdited'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: 'Sukses',
                    description: 'Berhasil mengedit data',
                    icon: 'success',
                    timeout: 3000
                })
            })
        </script>
    @elseif (session('addressDeleted'))
        <script>
            Wireui.hook('notifications:load', () => {
                window.$wireui.notify({
                    title: 'Sukses',
                    description: 'Berhasil menghapus data',
                    icon: 'success',
                    timeout: 3000
                })
            })
        </script>
    @endif --}}
</div>
