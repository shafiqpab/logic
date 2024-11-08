<style>
* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-size: 16px;
  font-family: Helvetica, Arial, Sans-Serif;
  background-color: #ddd;
}

a {
  text-decoration: none;
}

.container {
  width: 98%;
  height: 100%;
  margin: 0 auto;
  display: block;
}

.col {
  width: 100%;
  padding: 5%;
  background-color: red;
  margin-bottom: 5%;
  border-top: 5px solid red;
}

.h1, p {
  color: #333;
  text-align: left;
}

h1 {
  font-size: 1em;
  line-height: 1em;
}

p {
  font-size: 1em;
  line-height: 1em;
  color: white;
}

a {
  color: inherit;
}
a:hover {
  color: white;
}

/* the blue circle with only one line of text, centered vertically */
.oneline:after {
  content: "";
  display: block;
  width: 100%;
  height: 0;
  padding-bottom: 100%;
  background: cadetblue;
  -moz-border-radius: 50%;
  -webkit-border-radius: 50%;
  border-radius: 50%;
}
.oneline:hover:after {
  background-color: goldenrod;
}
.oneline div {
  float: left;
  width: 100%;
  padding-top: 50%;
  line-height: 1em;
  margin-top: -1.7em;
  text-align: center;
  color: white;
}
.oneline p {
  text-align: center;
  font-size: 1.2em;
}

@media only screen and (min-width: 350px) {
  .col {
    display: inline-block;
    position: relative;
    width: 30%;
    margin: 4% 0 5% 3%;
    padding: 0;
    background-color: transparent;
    border-top: none;
  }
  .col .spacer {
    position: relative;
    padding-top: 100%;
  }
  .col:nth-child(3n+1) {
    margin-left: 1%;
  }

  .circle {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: powderblue;
    color: #333;
    border-radius: 50%;
    box-sizing: border-box;
    text-align: center;
    display: block;
    padding: 5px;
    border: 5px solid #9dd8e0;
    -webkit-transition: all 1s ease-out;
    -moz-transition: all 1s ease-out;
    -o-transition: all 1s ease-out;
    transition: all 1s ease-out;
  }
  .circle:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -2%;
  }
  .circle:hover {
    background-color: maroon;
    border: 5px solid #4d0000;
    color: #fff;
  }

  .outline {
    border: 5px solid grey;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    border-radius: 50%;
    box-sizing: border-box;
  }

  .circle h1 {
    font-size: 0.9em;
    line-height: 1em;
  }
  .circle p {
    font-size: 0.8em;
    line-height: 1em;
    color: white;
    text-align: center;
  }
  .circle a {
    color: inherit;
  }
  .circle > p, .circle > h1, .circle > .wrapcontent {
    display: inline-block;
    vertical-align: middle;
  }
  .circle > p > p, .circle > p > h1, .circle > h1 > p, .circle > h1 > h1, .circle > .wrapcontent > p, .circle > .wrapcontent > h1 {
    display: block;
  }
}
@media only screen and (min-width: 481px) {
  .circle h1 {
    font-size: 1.2em;
    line-height: 1em;
  }
  .circle p {
    font-size: 1em;
    line-height: 1em;
  }
}
@media only screen and (min-width: 768px) {
  .circle h1 {
    font-size: 1.6em;
    line-height: 1em;
  }
  .circle p {
    font-size: 1.2em;
    line-height: 1em;
  }
}
</style>


<div class="container">  

      <article class="col oneline">
        <div class="circle-in"><p>only one line</p></div>
      </article>
      <article class="col">
        <div class="spacer"></div>
        <div class="circle"><h1><a href="#">Try resizing the window to see grow and shrink the circles</a></h1> </div>
      </article>
      <article class="col">
        <div class="spacer"></div>
        <div class="circle"><div class="wrapcontent"><h1><a href="#">This is an h1 text.</a></h1><p>And this is a paragraph text</p>
          </div>
        </div>
      </article>
      <article class="col">
        <div class="spacer"></div>
        <div class="circle"><div class="wrapcontent"
          <h1><a href="#">This is a long h1 text in more than two lines, maybe there are four.</a></h1></div> </div>
      </article>
      <article class="col">
        <div class="spacer"></div>
       <div class="outline"> <div class="circle">
          <h1><a href="#">Without wrapcontent. Only works in big resolutions</a></h1> </div></div>
      </article>
   <article class="col">
        <div class="spacer"></div>
        <div class="circle"> 
          <h1><a href="#">This text is out of the circle because there is space between .circle and child element in html</a></h1>
          </div>
      </article>
</div>