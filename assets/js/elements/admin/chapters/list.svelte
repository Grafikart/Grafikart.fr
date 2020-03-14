<script>
  import {onMount, tick} from 'svelte'
  import Sortable from 'sortablejs'

  export let chapters = []

  let ul

  // Lorsque la liste est réorganisé
  async function onListChange () {
    // On construit la nouvelle structure des chapitres
    const newChapters = []
    Array.from(ul.children).forEach(li => {
      newChapters.push({
        title: li.dataset.title,
        courses: Array.from(li.querySelectorAll('li')).map(l => {
          return {
            id: l.dataset.id,
            title: l.dataset.title
          }
        })
      })
    })

    chapters = newChapters
    console.log(chapters)
    await tick()
    console.log(chapters)
  }

  onMount(function () {
    const lists = Array.from(ul.querySelectorAll('ul'))
    lists.push(ul)
    lists.forEach(function (u) {
      new Sortable(u, {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onEnd: onListChange
      })
    })
  })
</script>

<ul class="chapters-editor stack-large" bind:this={ul}>
  {#each chapters as chapter}
  <li data-title={chapter.title}>
    <input class="chapters-editor__chapter" bind:value={chapter.title} />
    <ul>
      {#each chapter.courses as course}
        <li class="chapters-editor__course" data-title={course.title} data-id={course.id}>{course.title}</li>
      {/each}
      <li on:click={() => addChapter(chapter)}></li>
    </ul>
  </li>
  {/each}
</ul>
