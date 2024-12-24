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
        <div v-for="user in users" :key="user.user_id" :style="{ color: user.color }">
            {{ user.name }}
        </div>
    </div>
</template>

<script>
import * as fabric from 'fabric';
import { mapState } from 'vuex';
function updateCanvas(data) {
    const canvas = new fabric.Canvas('drawingCanvas');
}


export default {
    data() {
        return {
            clientId: this.generateUUID(),
            canvas: null,
            currentTool: 'draw',
            brushColor: 'black',
            brushWidth: 5,
            isProcessingUpdate: false,
            users: [],
            userId: null,
            drawingId: null,
        };
    },
    mounted() {
        this.canvas = new fabric.Canvas('drawingCanvas');
        this.canvas.isDrawingMode = true;
        this.canvas.selection = true;

       /* this.userId = this.$route.query.userId;
        this.drawingId = this.$route.query.drawingId;*/
        this.userId = 4564;
        this.drawingId = 999;

        this.canvas.on('object:modified', (e) => {
            const object = e.target;
            this.sendCanvasUpdate(object);
        });

        this.canvas.on('object:added', (e) => {
            const object = e.target;
            this.sendCanvasUpdate(object);
        });

        axios.post('/join-panel', {
            name: this.userName,
            color: this.userColor,
            drawing_id: this.drawingId,
        }).then(() => {
            console.log(this.userName + ' joined to the canvas!');
        });

        Echo.channel('drawing' + this.drawingId)
            .listen('StrokeDrawn', (event) => {
                this.drawStrokeOnCanvas(event);
            })
            .listen('UserJoined', (event) => {
                this.addUser(event.user_id, event.name, event.color);
            })
            .listen('UserLeft', (event) => {
                this.removeUser(event.user_id);
            });

        Echo.channel('drawing').listen('DrawingEvent', (event) => {
            if (event.origin != this.clientId) {
                this.handleDrawingEvent(event);
            }
        });
    },
    methods: {
        setupDrawing() {
            this.canvas.on('mouse:down', (e) => {
                if (!e.pointer) return;
                const { x, y } = e.pointer;
                this.broadcastStroke(x, y);
            });
        },
        drawStrokeOnCanvas(event) {
            const { x, y, color, stroke_width } = event;
            const line = new Fabric.Line([x, y, x, y], {
                stroke: color,
                strokeWidth: stroke_width,
                selectable: false,
            });
            this.canvas.add(line);
        },
        broadcastStroke(x, y) {
            const strokeData = {
                canvas_id: this.drawingId,
                x,
                y,
                color: '#ff0000', // example color
                stroke_width: 5, // example stroke width
            };

            axios.post('/draw-stroke', strokeData);
        },
        beforeDestroy() {
            // Leave the canvas when user exits
            axios.post('/leave-canvas', { canvas_id: this.drawingId });
        },


        generateUUID() {
            return Math.random().toString(36).substr(2, 9);
        },
        addUser(userId, name, color) {
            this.users.push({ userId, name, color });
        },
        removeUser(userId) {
            this.users = this.users.filter(user => user.userId !== userId);
        },
        handleDrawingEvent(event) {
            console.log('event received');
            this.setTool(event.tool);
        },
        sendCanvasUpdate(object) {
            if (this.isProcessingUpdate) return;

            this.isProcessingUpdate = true;

            const data = {
                id: object.id || this.generateUUID(),
                type: object.type,
                left: object.left,
                top: object.top,
                width: object.width,
                height: object.height,
                scaleX: object.scaleX,
                scaleY: object.scaleY,
                angle: object.angle,
                text: object.text || null,
                fill: object.fill || null,
                stroke: object.stroke || null,
            };

            axios.post('/update-drawing', {
                tool: this.currentTool,
                updateData : data,
                origin: this.clientId,
            }).then(() => {
                console.log('Canvas update sent to server.');
                this.isProcessingUpdate = false;
            });
        },
        setTool(tool) {
            this.currentTool = tool;

            this.canvas.isDrawingMode = false;
            this.canvas.selection = true;

            if (tool === 'text') {
                let data = {
                    left: 50,
                    top: 50,
                    width: 200,
                    fontSize: 20
                };
                this.addText(data);
            } else if (tool === 'rectangle') {
                let data = {
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
                };
                this.addRectangle(data);
            }
        },
        addText(data) {
            if (!data.id) {
                data.id = this.generateUUID();
            }
            const text = new fabric.Textbox('Type here!', data);
            this.canvas.add(text);
            this.canvas.setActiveObject(text);
        },
        addRectangle(data) {
            if (!data.id) {
                data.id = this.generateUUID();
            }
            const rect = new fabric.Rect(data);

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
