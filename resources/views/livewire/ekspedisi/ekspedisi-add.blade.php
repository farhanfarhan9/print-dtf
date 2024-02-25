<div>
    <form wire:submit.prevent="save">
        <div class="flex space-x-4">
            <!-- Nama Ekspedisi -->
            <div class="flex-1">
                <x-input label="Nama Ekspedisi" placeholder="Nama ekspedisi" wire:model="namaEkspedisi" />
            </div>

            <!-- Ongkir -->
            <div class="flex-1">
                <x-input class="!pl-[2.5rem]" label="Ongkir" placeholder="Ongkir" prefix="Rp." wire:model="ongkir" />
            </div>
        </div>

        <!-- Save button -->
        <div class="mt-4">
            <x-button label="Simpan" primary wire:click.prevent="save" />
        </div>
    </form>
</div>
