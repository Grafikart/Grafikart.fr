@props(['video', 'poster'])

<lazy-video video="{{$video}}" {{ $attributes->merge(['class' => "
    grid place-items-center overflow-hidden aspect-video group relative rounded-md shadow-lg cursor-pointer bg-[#000] hover:shadow-md transition-shadow" ])}}>
    <img
        alt=""
        loading="lazy"
        class="aspect-video w-full object-cover [grid-area:1/1]"
        src="{{$poster ?? "https://img.youtube.com/vi/{$video}/hqdefault.jpg"}}"
    />
    <div class="inset-0 absolute bg-linear-to-b from-transparent to-video group-hover:opacity-80 transition-opacity [grid-area:1/1]"></div>
    <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46 46" class="size-20 [grid-area:1/1] fill-white group-hover:scale-110 transition-all" style="filter: drop-shadow(0 1px 20px #121C4280);">
        <path
            d="M23 0C10.32 0 0 10.32 0 23s10.32 23 23 23 23-10.32 23-23S35.68 0 23 0zm8.55 23.83l-12 8A1 1 0 0118 31V15a1 1 0 011.55-.83l12 8a1 1 0 010 1.66z"></path>
    </svg>
</lazy-video>
