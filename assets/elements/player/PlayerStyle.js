export const playerStyle = `
:host {
  display: block;
}
.ratio {
  background-color:black;
  position: relative;
}
.ratio-svg {
  width: 100%;
  height: auto;
  display: block;
  max-height: var(--max-height, 100vh);
}
video ~ .ratio-svg,
iframe ~ .ratio-svg {
  max-height: 100%;
}
.poster {
  border: none;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.poster:hover .play {
  transform: scale(1.1)
}
.poster:hover::before {
  opacity: .8;
}
.title {
  margin-top: 4px;
  color: #FFF;
  font-size: 22px;
  position: relative;
  text-align: center;
  z-index: 3;
  transition: .3s;
  line-height: 1.2em;
}
.title em {
  display:block;
  opacity: 0.7;
  font-size: 0.8em;
  font-style: normal;
}
.play {
  position: relative;
  width: 48px;
  height: 48px;
  z-index: 3;
  fill: #FFF;
  margin-bottom: 8px;
  filter:  drop-shadow(0 1px 20px #121C4280);
  transition: .3s;
}
.poster::before {
  content:'';
  background: linear-gradient(to top, var(--color) 0%, var(--color-transparent) 100%);
  z-index: 2;
}
.poster,
iframe,
video,
.poster::before,
img {
  position: absolute;
  top:0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  transition: opacity .5s;
}
.poster img {
  object-fit: cover;
}
.poster[aria-hidden] {
  pointer-events: none;
  opacity: 0;
}`
