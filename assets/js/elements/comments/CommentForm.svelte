<script>
  import {slide} from 'svelte/transition'

  export let onSubmit
  export let loading
  export let scroll = false

  let form
  let input
  let data = {
    username: 'John',
    email: 'john@doe.fr',
    content: 'Mon contenu'
  }

  function formSubmit (e) {
    e.preventDefault()
    onSubmit(data)
  }

  function scrollToForm () {
    if (scroll === false) {
      return
    }
    input.focus()
    const rect = form.getBoundingClientRect()
    window.scrollTo({
      top: rect.top + window.scrollY - 100,
      left: 0,
      behavior: 'smooth'
    })
  }
</script>


<form action="" class="grid" style="--col: 300px;" on:submit={formSubmit} bind:this={form} transition:slide
      on:introend={scrollToForm}>
  <div class="form-group">
    <label for="firstname">Nom d'utilisateur</label>
    <input type="text" id="firstname" required bind:value={data.username} bind:this={input}>
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" required bind:value={data.email}>
  </div>
  <div class="form-group full">
    <textarea placeholder="Votre message" is="textarea-autogrow" required bind:value={data.content}></textarea>
  </div>
  <div class="full">
    {#if loading}
      <button class="btn-gradient" disabled>
        <spinning-dots class="icon"></spinning-dots>
        Envoyer
      </button>
    {:else}
      <button class="btn-gradient">Envoyer</button>
    {/if}
  </div>
</form>
