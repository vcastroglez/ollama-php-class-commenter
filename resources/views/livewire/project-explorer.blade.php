<div >
    <form wire:submit.prevent="analyze" >
        <input type="file" wire:model="class_file" >

        @error('class_file') <span class="error" >{{ $message }}</span > @enderror

        <button type="submit" >Analyze file</button >
    </form >

    {{ json_encode($commented_class) }}
</div >
