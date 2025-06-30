@extends('layouts.authenticated')
@section('title')| Dashboard @endsection
@section('content')

<div>
    DASHBOARD

    <div id="pallete" class="w-96 h-96" style="background-color: rgb(255, 0, 0);"></div>
    <div id="color-code">rgb(255, 0, 0)</div>
    <input type="range" name="" id="" value="0" min="0" max="1530" class="w-96" oninput="changeColor(this)">
</div>

<script>
    const increaseSpectrum = (minBrightness, maxBrightness, value, stepValue) => {
    // const increaseSpectrum = (minBrightness, maxBrightness, value, step, stepValue) => {
        // return minBrightness + (maxBrightness * ((value - stepValue*step) / stepValue))
        return minBrightness + (maxBrightness * (value / stepValue))
    }
    // const decreaseSpectrum = (minBrightness, maxBrightness, value, step, stepValue) => {
    const decreaseSpectrum = (minBrightness, maxBrightness, value, stepValue) => {
        const brightnessRange = maxBrightness - minBrightness
        // return maxBrightness - (brightnessRange * ((value - stepValue*step) / stepValue))
        return maxBrightness - (brightnessRange * (value / stepValue))
    }

    const changeColor = (e) => {
        const {value, max} = e
        if (value < 0) return
        const stepCount = 6
        const stepValue = max / stepCount
        const minBrightness = 0
        const maxBrightness = 255
        const brightnessRange = maxBrightness - minBrightness

        const rgb = {
            red: minBrightness,
            green: minBrightness,
            blue: minBrightness
        }
        
        if (value <= stepValue) {
            rgb.red = maxBrightness
            rgb.green = increaseSpectrum(minBrightness, maxBrightness, value, stepValue)
        } else if (value > stepValue && value <= stepValue*2) {
            rgb.red = decreaseSpectrum(minBrightness, maxBrightness, value - stepValue, stepValue)
            rgb.green = maxBrightness
        } else if (value > stepValue*2 && value <= stepValue*3) {
            rgb.green = maxBrightness
            rgb.blue = increaseSpectrum(minBrightness, maxBrightness, value - stepValue*2, stepValue)
        } else if (value > stepValue*3 && value <= stepValue*4) {
            rgb.green = decreaseSpectrum(minBrightness, maxBrightness, value - stepValue*3, stepValue)
            rgb.blue = maxBrightness
        } else if (value > stepValue*4 && value <= stepValue*5) {
            rgb.red = increaseSpectrum(minBrightness, maxBrightness, value - stepValue*4, stepValue)
            rgb.blue = maxBrightness
        } else if (value > stepValue*5 && value <= stepValue*6) {
            rgb.red = maxBrightness
            rgb.blue = decreaseSpectrum(minBrightness, maxBrightness, value - stepValue*5, stepValue)
        }
        const color = `rgb(${rgb.red}, ${rgb.green}, ${rgb.blue})`
        document.getElementById('pallete').style.backgroundColor = color
        document.getElementById('color-code').innerHTML = color
        return
    }
</script>
@endsection