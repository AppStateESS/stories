'use strict'
import EditorJS from '@editorjs/editorjs'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import Image from '@editorjs/image'
import List from '@editorjs/list'
import {Paragraph} from '@editorjs/paragraph'

const editor = new EditorJS({
  tools: {header: Header, embed: Embed, list: List, image: Image},
})
