# How to Generate Help Files - Instructions

## markdown-include

Photon CMS documentation is built using [markdown-include](https://www.npmjs.com/package/markdown-include) Node package. It allows us to include markdown files into other markdown files using a C style include syntax.

For example, you could place the following into a markdown file:

```
 #include "markdown-file.md"
 #include "another-markdown-file.md"
```

And assuming that markdown.file.md contents are:

```
Something in markdown file!
```

And assuming that another-markdown-file.md contents are:

```
Something in another markdown file!
```

It would compile to:

```
Something in markdown file!
Something in another markdown file!
```

## How To Use From The Command Line

*markdown-include* does require that you define a markdown.json file with your options for compile. 

Run from the command line project root to compile your documents like so:

`node_modules/markdown-include/bin/cli.js resources/assets/photonCms/dependencies/js/help/markdown.json`

## .md files

Source Markdown files are located under `resources/assets/photonCms/dependencies/js/help/SourceFiles` folder. To start editing open `Main.md` and from there include any other .md file.
