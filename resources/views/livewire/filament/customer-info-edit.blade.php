<x-filament::section>
    <form wire:submit="create">
        {{ $this->form }}

       <div class="pt-4">
           <x-filament::button class="mt-4" type="submit" icon="heroicon-m-bookmark" wire:loading.attr="disabled">
               Lưu
               <div class="flex items-center justify-center" wire:loading>
                   <x-filament::loading-indicator class="h-5 w-5" />
               </div>
           </x-filament::button>
       </div>
    </form>
</x-filament::section>


