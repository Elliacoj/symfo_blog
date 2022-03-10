export {Title}

class Title {
    /**
     * Constructor
     */
    constructor() {
        this.title = document.getElementById("title");
    }

    /**
     * Init the title object
     */
    init() {
        this.animation();
    }

    /**
     * Anime the title
     */
    animation() {
        if(this.title) {
            this.title.animate([
                    { right: "+65%" },
                    { right: "-60%" }
                ], {
                    duration: 10000,
                    iterations: Infinity
                }
            )
        }
    }
}