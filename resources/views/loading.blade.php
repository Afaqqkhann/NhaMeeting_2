<style>
.loader {
  color:grey;
  font-family:sans-serif;
  height:5vh;
  left:50%;
  overflow-x:hidden;
  padding-top:7vh;
  position:absolute;
  text-align:center;
  top:50%;
  transform:translate(-50%,-50%);
  width:50vw;
  &:before {
    animation:left-to-right 3s alternate infinite linear;
    background-image:linear-gradient(to right, transparent, orange, transparent);
    // bottom:0;
    content:"";
    height:5vh;
    left:0;
    overflow:hidden;
    position:absolute;
    right:0;
    top:0;
  }
  &:after {
    background-image:repeating-linear-gradient(
      90deg,
      white,
      white 1vw,
      transparent 1vw,
      transparent 10vw
    );
    content:"";
    height:5vh;
    left:0;
    overflow:hidden;
    position:absolute;
    right:0;
    top:0;
  }
}

@keyframes left-to-right {
  0% {
    transform:translateX(-100%);
  }
  100% {
    transform:translateX(100%);
  }
}
</style>

<div class="loader">Loading...</div>