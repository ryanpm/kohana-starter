chai.should()

describe "Dropzone", ->

  describe "static functions", ->

    describe "Dropzone.createElement()", ->

      element = Dropzone.createElement """<div class="test"><span>Hallo</span></div>"""

      it "should properly create an element from a string", ->
        element.tagName.should.equal "DIV"
      it "should properly add the correct class", ->
        element.classList.contains("test").should.be.ok
      it "should properly create child elements", ->
        element.querySelector("span").tagName.should.equal "SPAN"
      it "should always return only one element", ->
        element = Dropzone.createElement """<div></div><span></span>"""
        element.tagName.should.equal "DIV"

    describe "Dropzone.elementInside()", ->
      element = Dropzone.createElement """<div id="test"><div class="child1"><div class="child2"></div></div></div>"""
      document.body.appendChild element

      child1 = element.querySelector ".child1"
      child2 = element.querySelector ".child2"

      after -> document.body.removeChild element

      it "should return yes if elements are the same", ->
        Dropzone.elementInside(element, element).should.be.ok
      it "should return yes if element is direct child", ->
        Dropzone.elementInside(child1, element).should.be.ok
      it "should return yes if element is some child", ->
        Dropzone.elementInside(child2, element).should.be.ok
        Dropzone.elementInside(child2, document.body).should.be.ok
      it "should return no unless element is some child", ->
        Dropzone.elementInside(element, child1).should.not.be.ok
        Dropzone.elementInside(document.body, child1).should.not.be.ok

    describe "Dropzone.optionsForElement()", ->
      testOptions =
        url: "/some/url"
        method: "put"

      before -> Dropzone.options.testElement = testOptions
      after -> delete Dropzone.options.testElement

      element = document.createElement "div"

      it "should take options set in Dropzone.options from camelized id", ->
        element.id = "test-element"
        Dropzone.optionsForElement(element).should.equal testOptions

      it "should return undefined if no options set", ->
        element.id = "test-element2"
        expect(Dropzone.optionsForElement(element)).to.equal undefined

    describe "Dropzone.forElement()", ->
      element = document.createElement "div"
      element.id = "some-test-element"
      dropzone = null
      before ->
        document.body.appendChild element
        dropzone = new Dropzone element, url: "/test"
      after ->
        dropzone.disable()
        document.body.removeChild element

      it "should return null if no dropzone attached", ->
        expect(Dropzone.forElement document.createElement "div").to.equal null

      it "should accept css selectors", ->
        expect(Dropzone.forElement "#some-test-element").to.equal dropzone

      it "should accept native elements", ->
        expect(Dropzone.forElement element).to.equal dropzone

    describe "Dropzone.discover()", ->
      element1 = document.createElement "div"
      element1.className = "dropzone"
      element2 = element1.cloneNode()
      element3 = element1.cloneNode()

      element1.id = "test-element-1"
      element2.id = "test-element-2"
      element3.id = "test-element-3"

      describe "specific options", ->
        before ->
          Dropzone.options.testElement1 = url: "test-url"
          Dropzone.options.testElement2 = false # Disabled
          document.body.appendChild element1
          document.body.appendChild element2
          Dropzone.discover()
        after ->
          document.body.removeChild element1
          document.body.removeChild element2

        it "should find elements with a .dropzone class", ->
          element1.dropzone.should.be.ok

        it "should not create dropzones with disabled options", ->
          expect(element2.dropzone).to.not.be.ok

      describe "Dropzone.autoDiscover", ->
        before ->
          Dropzone.options.testElement3 = url: "test-url"
          document.body.appendChild element3
        after ->
          document.body.removeChild element3

        it "should not create dropzones if Dropzone.autoDiscover == false", ->
          Dropzone.autoDiscover = off
          Dropzone.discover()
          expect(element3.dropzone).to.not.be.ok


  describe "constructor()", ->

    it "should throw an exception if the element is invalid", ->
      expect(-> new Dropzone "#invalid-element").to.throw "Invalid dropzone element."

    it "should throw an exception if assigned twice to the same element", ->
      element = document.createElement "div"
      new Dropzone element, url: "url"
      expect(-> new Dropzone element, url: "url").to.throw "Dropzone already attached."

    it "should set itself as element.dropzone", ->
      element = document.createElement "div"
      dropzone = new Dropzone element, url: "url"
      element.dropzone.should.equal dropzone

    describe "options", ->
      element = null
      element2 = null
      beforeEach ->
        element = document.createElement "div"
        element.id = "test-element"
        element2 = document.createElement "div"
        element2.id = "test-element2"
        Dropzone.options.testElement = url: "/some/url", parallelUploads: 10
      afterEach -> delete Dropzone.options.testElement

      it "should take the options set in Dropzone.options", ->
        dropzone = new Dropzone element
        dropzone.options.url.should.equal "/some/url"
        dropzone.options.parallelUploads.should.equal 10

      it "should prefer passed options over Dropzone.options", ->
        dropzone = new Dropzone element, url: "/some/other/url"
        dropzone.options.url.should.equal "/some/other/url"

      it "should take the default options if nothing set in Dropzone.options", ->
        dropzone = new Dropzone element2, url: "/some/url"
        dropzone.options.parallelUploads.should.equal 2

      describe "options.clickable", ->
        clickableElement = null
        beforeEach ->
          clickableElement = document.createElement "div"
          clickableElement.className = "some-clickable"
          document.body.appendChild clickableElement
        afterEach ->
          document.body.removeChild clickableElement

        it "should use the default element if clickable == true", ->
          dropzone = new Dropzone element, clickable: yes
          dropzone.clickableElement.should.equal dropzone.element
        it "should lookup the element if clickable is a CSS selector", ->
          dropzone = new Dropzone element, clickable: ".some-clickable"
          dropzone.clickableElement.should.equal clickableElement
        it "should simply use the provided element", ->
          dropzone = new Dropzone element, clickable: clickableElement
          dropzone.clickableElement.should.equal clickableElement
        it "should throw an exception if the element is invalid", ->
          expect(-> new Dropzone element, clickable: ".some-invalid-clickable").to.throw "Invalid `clickable` element provided. Please set it to `true`, a plain HTML element or a valid CSS selector."

      it "should call the fallback function if forceFallback == true", (done) ->
        dropzone = new Dropzone element,
          url: "/some/other/url"
          forceFallback: on
          fallback: -> done()


  describe "init()", ->
    describe "clickable", ->
      element = Dropzone.createElement """<form action="/"></form>"""
      dropzone = new Dropzone element, clickable: yes
      it "should create a hidden file input if clickable", ->
        dropzone.hiddenFileInput.should.be.ok
        dropzone.hiddenFileInput.tagName.should.equal "INPUT"
      it "should create a new input element when something is selected to reset the input field", ->
        for i in [0..3]
          hiddenFileInput = dropzone.hiddenFileInput
          event = document.createEvent "HTMLEvents"
          event.initEvent "change", true, true
          hiddenFileInput.dispatchEvent event
          dropzone.hiddenFileInput.should.not.equal hiddenFileInput
          Dropzone.elementInside(hiddenFileInput, document).should.not.be.ok


  describe "helper function", ->
    describe "getExistingFallback()", ->
      element = null
      dropzone = null
      beforeEach ->
        element = Dropzone.createElement """<div></div>"""
        dropzone = new Dropzone element, url: "url"

      it "should return undefined if no fallback", ->
        expect(dropzone.getExistingFallback()).to.equal undefined

      it "should only return the fallback element if it contains exactly fallback", ->
        element.appendChild Dropzone.createElement """<form class="fallbacks"></form>"""
        element.appendChild Dropzone.createElement """<form class="sfallback"></form>"""
        expect(dropzone.getExistingFallback()).to.equal undefined

      it "should return divs as fallback", ->
        fallback = Dropzone.createElement """<form class=" abc fallback test "></form>"""
        element.appendChild fallback
        fallback.should.equal dropzone.getExistingFallback()
      it "should return forms as fallback", ->
        fallback = Dropzone.createElement """<div class=" abc fallback test "></div>"""
        element.appendChild fallback
        fallback.should.equal dropzone.getExistingFallback()


