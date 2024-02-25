<div>
    <form wire:submit.prevent="save">
        <div class="flex space-x-4">
            <!-- Nama Ekspedisi -->
            <div class="flex-1">
                <x-input label="Nama Ekspedisi" placeholder="Nama ekspedisi" wire:model.defer="namaEkspedisi" />
            </div>

            <!-- Ongkir -->
            <div class="flex-1">
                <x-input label="Ongkir" prefix="Rp." placeholder="Ongkir" wire:model.defer="ongkir" />
            </div>
        </div>

        <!-- Save button -->
        <div class="mt-4">
            <x-button label="Simpan" primary wire:click.prevent="save" />
        </div>
    </form>
</div>
