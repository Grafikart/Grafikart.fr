<script>
  import {findAllComments, addComment, deleteComment} from '../../api/comments'
  import CommentForm from './CommentForm.svelte'
  import {slide} from 'svelte/transition'
  import Icon from './Icon.svelte'

  export let target

  let comments = null
  let isLoading = false
  let reply = null

  async function loadComments () {
    try {
      const c = await findAllComments(target)
      comments = c.sort((a,b) => b.createdAt - a.createdAt)
    } catch (e) {
      alert(e.detail)
    }
  }

  async function onSubmit (data) {
    isLoading = true
    try {
      const comment = await addComment({target: target, ...data})
      comments = [comment, ...comments]
    } catch (e) {
      alert(e.detail)
    }
    isLoading = false
  }

  /**
   * @param {CommentResource} comment
   * @return {Promise<void>}
   */
  async function onDelete (comment) {
    comment.loading = true
    comments = comments // Force svelte à tracker le changement
    try {
      await deleteComment(comment.id)
      comments = comments.filter(c => c !== comment)
    } catch (e) {
      alert(e.detail)
    }
  }

  function scrollTo(node) {
    return {
      duration: 500,
      tick: t => {
        if (t === 1) {
          window.scrollTo({
            top: node.getBoundingClientRect().top,
            left: 0,
            behavior: 'smooth'
          })
        }
      }
    };
  }

  function replyTo(comment) {
    reply = comment
  }

  loadComments()
</script>

{#if comments === null}
<div class="comments-loader">
  <spinning-dots></spinning-dots>
</div>
{:else}
  <aside class="comment-area">
    <div class="comments__title">{comments.length} Commentaires</div>

    <CommentForm loading={isLoading} onSubmit={onSubmit}></CommentForm>

    <hr>

    <div class="comment-list">
      {#each comments as comment (comment.id)}
      <div class="comment" transition:slide class:is-loading={comment.loading}>
        {#if comment.loading}
        <spinning-dots class="comment__loader"></spinning-dots>
        {/if}
        <img src="{comment.avatar}" alt="" class="comment__avatar">
        <div class="comment__meta">
          <div class="comment__author">{comment.username}</div>
          <div class="comment__actions">
            <a class="comment__date" href="#c{comment.id}">
              <time-ago time="{comment.createdAt}"></time-ago>
            </a>
            <a href="#c{comment.id}" on:click|preventDefault={() => replyTo(comment)}>
              <Icon name="reply"></Icon>
              Répondre
            </a>
            <a href="#c{comment.id}" on:click|preventDefault={() => onDelete(comment)}>
              <Icon name="trash"></Icon>
              Supprimer
            </a>
          </div>
        </div>
        <div class="comment__content form-group">{comment.content}</div>
        <div class="comment__replies">
          {#if reply === comment}
            <CommentForm loading={isLoading} onSubmit={onSubmit} scroll={true}></CommentForm>
          {/if}
        </div>
      </div>
      {/each}
    </div>


  </aside>
{/if}
