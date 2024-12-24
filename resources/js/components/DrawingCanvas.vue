<template>
    <div>
        <h1>Collaborative Drawing</h1>
        
        <div id="tools">
            <button @click="setTool('text')">Text</button>
            <button @click="setTool('rectangle')">Rectangle</button>
        </div>

        <div id="actions">
            <button @click="deleteSelectedObject">Delete</button>
        </div>
        
        <!-- Canvas -->
        <canvas id="drawingCanvas" width="1024px" height="600px"></canvas>
    </div>
</template>

<script>
import * as fabric from 'fabric';

function updateCanvas(data) {
    const canvas = new fabric.Canvas('drawingCanvas');
}


export default {
    data() {
        return {
            canvas: null,
            currentTool: 'draw',
            brushColor: 'black',
            brushWidth: 5,
        };
    },
    mounted() {
        this.canvas = new fabric.Canvas('drawingCanvas');
        this.canvas.isDrawingMode = true;

        this.canvas.selection = true;
    },
    methods: {
        setTool(tool) {
            this.currentTool = tool;

            if (tool === 'text') {
                this.canvas.isDrawingMode = false;
                this.canvas.selection = true;
                this.addText();
            } else if (tool === 'rectangle') {
                this.canvas.isDrawingMode = false;
                this.canvas.selection = true;
                this.addRectangle();
            }

            axios.post('/change-tool', { tool }).then(() => {
                console.log('Sent to backend.');
            });
        },
        addText() {
            const text = new fabric.Textbox('Type here!', {
                left: 50,
                top: 50,
                width: 200,
                fontSize: 20
            });
            this.canvas.add(text);
            this.canvas.setActiveObject(text);
        },
        addRectangle() {
            const rect = new fabric.Rect({
                left: 100,
                top: 50,
                fill: 'yellow',
                width: 200,
                height: 100,
                objectCaching: false,
                stroke: this.brushColor,
                strokeWidth: 4,
                hasBorders: true,
                hasControls: true,
            });

            this.canvas.add(rect);
            this.canvas.setActiveObject(rect);
        },
        deleteSelectedObject() {
            const activeObject = this.canvas.getActiveObject();
            if (activeObject) {
                this.canvas.remove(activeObject);
            }
        },
    },
};
</script>

<style scoped>
#drawingCanvas {
    border: 1px solid #000;
}
button {
    margin: 10px;
    padding: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
}
button:hover {
    background-color: #e0e0e0;
}
</style>
