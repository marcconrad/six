<!DOCTYPE html>
<html>

<head>
    <script>
        var version = "0.13";
    </script>
    </script>
    <title>6EQ Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">

    <style>
        #moreinfo {
            background-color: yellow;
            border-radius: 10px;
        }

        #captionsix {
            color: brown;
            font-weight: bold;
        }

        #info2 {
            color: brown;
        }

        #newgameserverbtn {
            background-color: lightcyan;
            border-radius: 10px;
        }

        
        #permatoday {
            background-color: lightsteelblue;
            border-radius: 10px;
        }

        #newgame {
            background-color: lightseagreen;
            border-radius: 10px;
        }

        #sharebtn {
            background-color: yellow;
            border-radius: 10px;
        }

        #moreinfobtn {
            background-color: yellow;
            border-radius: 10px;
        }

        #checkbtn {
            background-color: lightpink;
            border-radius: 10px;
        }


        .ij {
            background-color: white;
            text-align: center;
        }

        .ijdiv {
            font-size: 20px;
            <?php
            if (isset($_GET["test"])) {
                echo ' background-color: yellow;';
            } else {
                // same color as digits to hide
                echo ' background-color: blue;';
            }
            echo "\r\n";

            ?>
            /* opacity: 0.5; */

            width: 1.3em;
            height: 1.3em;
        }

        #count {
            background-color: black;
            color: yellow;
            font-family: system-ui;
            border-radius: 5px;
            text-align: center;
        }

        .op {
            color: red;
        }

        .digit {
            color: blue;
        }
    </style>
    <script>
        <?php
        $count = 0;
        $ts  = -1;
        $now = "";

        if (isset($_GET["n"])) {
            $a = $_GET["n"];
            if ($a === "today") {
                date_default_timezone_set('UTC');
                $now = date('l jS \of F Y');
                $count = hexdec("123".md5($now));
            } else {
                $count = floatval($a);
              
                }  
            $count = abs($count); 
            while ($count > (1 << 30)) {
                    $count /= 99997;
            }
        } else {
            $countfile = "countlog4.txt";
            $datei = @fopen($countfile, "r");
            $data = @fgets($datei, 1000);
            if ($data === FALSE) {
                $data = "37,0";
            }
            @fclose($datei);
            list($count, $ts) = explode(',', $data);
            $count = $count + 1;
            $datei = fopen($countfile, "w");
            fwrite($datei, $count . "," . time());
            fclose($datei);
            echo "window.location.href = window.location.href+'?x=yes&n=$count';";
        }
        $u = $_GET["m"] ?? "normal";

        echo "var maincount = $count;";
        echo "\r\n";
        echo "var gamemode = '$u';";
        echo "\r\n";
        echo "var datetoday = '$now';";
        echo "\r\n";
        echo "var maints = $ts;";
        echo "\r\n";
        echo "\r\n";

        ?>
        // from: https://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript
        String.prototype.hashCode = function() {
            var hash = 0,
                i, chr;
            if (this.length === 0) return hash;
            for (i = 0; i < this.length; i++) {
                chr = this.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash |= 0; // Convert to 32bit integer
            }
            return hash;
        };
        var seed = 17.1;

        function seed_random() {
            var info = document.getElementById("count");
            var x = "ABC"+info.innerHTML+"plussomepaddingbecausethehashisrubbish"; 
            console.log("x="+x); 
            var m = Math.abs(x.hashCode());
            console.log("m="+m); 
            seed = parseFloat("0."+m); 
            console.log("seed="+seed);
        }

        function random() {

            var x = Math.sin(seed++) * 10000; // don't laugh. Good enough for what we need here. 
           // console.log(x);
            return x - Math.floor(x);
        }

        // from: https://stackoverflow.com/questions/23013573/swap-key-with-value-json
        /*
        function swap(json) {
            var ret = {};
            for (var key in json) {
                ret[json[key]] = key;
            }
            return ret;
        }
        */
        var op2html = {
            "p": '<b what="p" class="op">&plus;</b>',
            "m": '<b what="m" class="op">&minus;</b>',
            "x": '<b what="x" class="op">&times;</b>',
            "d": '<b what="d" class="op">&divide;</b>',
            "e": '<b what="e" class="op">&equals;</b>'
        };

        // var html2op = swap(op2html);




        function addOp(i, j, op) {
            t = document.getElementById("tdiv" + i + "x" + j);
            t.innerHTML = op2html[op];
        }

        function getOp(i, j) {
            // console.log(JSON.stringify(html2op)); 
            t = document.getElementById("tdiv" + i + "x" + j);
            // console.log("tinn="+t.innerHTML);
            var opid = t.childNodes[0].getAttribute('what');
            //         console.log("opid="+opid);
            return opid;
            // return html2op[t.innerHTML];
        }

        function dig2html(d) {
            aa = '<span class="digit" >' + d + '</span>';
            return aa;
        }

        function html2dig(divnode) {
            //  console.log("divnodeid=" + divnode.id);
            var aa = divnode.childNodes;
            if (aa.length != 1) {
                return 0;
            } else {
                ret = parseInt(aa[0].innerHTML);
                if (isNaN(ret)) {
                    return 0;
                }
                return ret;
            }
        }

        function isSwappable(divnode) {
            var aa = divnode.childNodes;
            if (aa.length != 1) {
                return false;
            } else {
                ret = parseInt(aa[0].innerHTML);
                if (isNaN(ret)) {
                    return false;
                }
                return ret;
            }
        }

        function check_equation(eq) {
            var op = eq[1];
            res = null;
            if (op == "p") {
                res = parseInt(eq[0]) + parseInt(eq[2]);
            } else if (op == "m") {
                res = parseInt(eq[0]) - parseInt(eq[2]);
            } else if (op == "x") {
                res = parseInt(eq[0]) * parseInt(eq[2]);
            } else if (op == "d") {
                res = parseInt(eq[0]) / parseInt(eq[2]);
            } else {
                return false;
            }
            // console.log("res=" + res);
            if (Math.abs(res - parseInt(eq[4])) < 0.0001) {
                return true;
            } else return false;
        }

        function invop(op) {
            if (op == "x") return "d";
            if (op == "p") return "m";
            if (op == "m") return "p";
            if (op == "d") return "x";
            return "o";
        }

        function trans_eq0(eq) {
            var ret = [];

            for (i = 0; i < 3; i++) {
                ret[i] = eq[i + 3];
                ret[i + 3] = eq[i];
            }

            return ret;
        }

        function trans_eq1(eq) {
            var ret = [];
            ret[2] = eq[0];
            ret[1] = eq[1];
            ret[0] = eq[2];
            for (i = 3; i < 6; i++) {
                ret[i] = [eq[i][4], invop(eq[i][1]), eq[i][2], "e", eq[i][0]];
            }
            return ret;
        }

        function trans_eq2(eq) {
            var ret = [];
            ret[5] = eq[3];
            ret[4] = eq[4];
            ret[3] = eq[5];
            for (i = 0; i < 3; i++) {
                ret[i] = [eq[i][4], invop(eq[i][1]), eq[i][2], "e", eq[i][0]];
            }
            return ret;
        }

        function trans_eq3(eq) {
            return eq;
        }

        function create_mode0(attempt = 0) { // Allow y negative
            var a = Math.floor(random() * 999);
            var b = Math.floor(random() * (999 - a));
            var x = Math.floor(random() * 999);
            var y = Math.floor(random() * 1999 - 999);
            var c = a + b;
            if (c > 999) {
                return false;
            }
            var z = x + y;
            if (z < 1 || z > 999) {
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }
            var v = b + y;
            if (v < 1 || v > 999) {
                return false;
            }
            var w = u + v;
            if (w > 999) {
                return false;
            }
            var yabs = Math.abs(y);
            var opy = "p";
            if (y < 0) {
                opy = "m";
            }

            var equations = [
                [a, "p", b, "e", c],
                [x, opy, yabs, "e", z],
                [u, "p", v, "e", w],
                [a, "p", x, "e", u],
                [b, opy, yabs, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;

        }

        function create_mode1(attempt = 0) {

            var a = Math.floor(random() * 999);
            var b = Math.floor(random() * a); // as a > b
            var x = Math.floor(random() * (999 - a)); // as a + x = u => a + x < 999 => x < 999 - a. 
            var y = Math.floor(random() * Math.min((999 - x), b)); // x + y < 999 and y < b

            if (y < 1 || b < 10) { // Reject some outliers.
                return false;
            }
            // console.log("a="+a+" b="+b+" x="+x)
            var v = b - y;
            if (v < 1) {
                return false;
            }
            var c = a - b; // a > b
            if (c < 1) {
                console.log("c=" + c);
                return false;
            }
            var u = a + x; // u > a and u > x
            if (u > 999) {
                console.log("u=" + u);
                return false;
            }
            var z = x + y; // x > y
            if (z < 1) {
                console.log("z=" + z);
                return false;

            }

            var w = u - v;
            if (w < 1) {
                console.log("w=" + w);
                return false;
            }
            var equations = [
                [a, "m", b, "e", c],
                [x, "p", y, "e", z],
                [u, "m", v, "e", w],
                [a, "p", x, "e", u],
                [b, "m", y, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;

        }

        function create_mode2(attempt = 0) { // a * b = c
            var c = Math.floor(random() * 999);
            var b = Math.floor(2 + random() * 66);
            var a = Math.floor(c / b);
            c = a * b;
            if (c > 999) {
                return false;
            }


            var x = Math.floor(random() * 999);
            var y2 = Math.floor(c - a - b - 2 * x);
            if (y2 < 0) {
                return false;
            }
            if (y2 % 2 != 0) {
                return false;
            }
            var y = Math.round(y2 / 2);

            var z = x + y;
            if (z > 999) {
                return false;
            }
            var w = c - z;
            if (w < 1) {
                return false;
            }
            var v = b + y;
            if (v > 999) {
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }

            var equations = [
                [a, "x", b, "e", c],
                [x, "p", y, "e", z],
                [u, "p", v, "e", w],
                [a, "p", x, "e", u],
                [b, "p", y, "e", v],
                [c, "m", z, "e", w],
            ];
            return equations;

        }





        function create_mode3(attempt = 0) { // a * b = c
            var c = Math.floor(random() * 999);
            var b = Math.floor(2 + random() * 60);
            var a = Math.floor(c / b);
            c = a * b;
            if (c > 999) {
                return false;
            }



            var y2 = Math.floor(c - a - b);
            if (y2 < 0) {
                return false;
            }
            if (y2 % 2 != 0) {
                return false;
            }
            var y = Math.round(y2 / 2);

            var x = Math.floor(random() * 999);

            var u = a + x;
            if (u > 999) {
                return false;
            }


            var z = x - y;
            if (z < 1) {
                return false;
            }
            var v = b + y;
            if (v > 999) {
                return false;
            }
            var w = c + z; // = u + v
            if (w > 999) {
                return false;
            }
            var equations = [
                [a, "x", b, "e", c],
                [x, "m", y, "e", z],
                [u, "p", v, "e", w],
                [a, "p", x, "e", u],
                [b, "p", y, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;
        }

        function create_mode4(attempt = 0) { // a * b = c
            var z = Math.floor(random() * 999);
            var x = Math.floor(2 + random() * 99);
            var y = Math.floor(z / x);
            z = x * y;
            if (z > 999) {
                return false;
            }
            var b2 = Math.floor(z - x - y);
            if (b2 < 0) {
                return false;
            }
            if (b2 % 2 != 0) {
                return false;
            }
            var b = Math.round(b2 / 2);
            var a = Math.floor(random() * 999);

            var c = a - b;
            if (c < 1) {
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }
            var v = b + y;
            if (v > 999) {
                return false;
            }
            var w = c + z; // = u + v
            if (w > 999) {
                return false;
            }


            var equations = [
                [a, "m", b, "e", c],
                [x, "x", y, "e", z],
                [u, "p", v, "e", w],
                [a, "p", x, "e", u],
                [b, "p", y, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;
        }

        function create_mode5(attempt = 0) { // a * b = c
            var b = Math.floor(random() * 999);
            var y = Math.floor(2 + random() * b);

            var v = b - y;
            if (v < 1) {
                return false;
            }
            var d = Math.abs(1 - b + y);
            if (d < 1) {
                return false;
            }
            var found = false;
            var zzz = 100;

            var tempz = random() * 999;
            var x = Math.floor(tempz / y);
            if (x < 2) {
                console.log("x too small. x=" + x);
                return false;
            }
            while (found === false && zzz-- > 0) {
                // tt = (b * x - 2 * x * y - b) % d; 
                if ((b * x - 2 * x * y - b) % d == 0) {
                    found = true;
                } else {
                    // x = Math.floor(random() * 999);
                    x++;
                }
            }
            if (found === false) {
                console.log("no x found.");
                return false;
            }
            if (x > 999) {
                console.log("x too big. x=" + x);
                return false;
            }
            console.log("yes x=" + x);
            var a = Math.round((b * x - 2 * x * y - b) / (1 - b + y));
            if (a < 2 || a > 999) {
                console.log("wrong a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
                return false;
            }
            console.log("good! a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
            var z = x * y;
            if (z > 999) {
                console.log("wrong z=" + z);
                return false;
            }

            var c = a + b;
            if (c > 999) {
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }

            var w = c + z; // = u + v
            if (w > 999) {
                return false;
            }

            var equations = [
                [a, "p", b, "e", c],
                [x, "x", y, "e", z],
                [u, "x", v, "e", w],
                [a, "p", x, "e", u],
                [b, "m", y, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;
        }

        function create_mode6(attempt = 0) { // a * b = c
            var b = 1 + Math.floor(random() * 99);
            var y = 1 + Math.floor(random() * (99 - b));

            var v = b + y;
            if (v > 999) {
                return false;
            }

            var x = Math.floor(random() * 99);
            var zzz = 100;
            found = false;
            while (found === false && zzz-- > 0) {
                d = Math.abs(x - b);
                if ((2 * b * x + b * y + x * y) % d == 0) {
                    found = true;
                } else {
                    x++;
                }
            }
            if (found === false) {
                console.log("no x found.");
                return false;
            }
            if (x > 999) {
                console.log("x too big. x=" + x);
                return false;
            }
            console.log("yes x=" + x);
            var a = Math.round((2 * b * x + b * y + x * y) / (x - b));
            if (a < 2 || a > 999) {
                console.log("wrong a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
                return false;
            }
            console.log("good! a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
            var z = x + y;
            if (z > 999) {
                console.log("wrong z=" + z);
                return false;
            }

            var c = a - b; //// should be minus
            if (c < 0) {
                console.log("wrong c=" + c);
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }

            var w = c * z; // = u + v
            if (w > 999) {
                console.log("wrong w=" + w);
                return false;
            }

            var equations = [
                [a, "m", b, "e", c],
                [x, "p", y, "e", z],
                [u, "x", v, "e", w],
                [a, "p", x, "e", u],
                [b, "p", y, "e", v],
                [c, "x", z, "e", w],
            ];
            return equations;
        }

        function create_mode7(attempt = 0) { // a / b = c
            var a = Math.floor(random() * 999);
            var b = 1 + Math.floor(random() * 60);
            var c = 1 + Math.floor(a / b);
            a = b * c;
            if (a > 999) {
                return false;
            }

            // Find x, such that x-1 divides (a+b+x–c)

            var x = 2 + Math.floor(random() * 99);
            var zzz = 100;
            found = false;
            while (found === false && zzz-- > 0) {
                var h = a + b + x - c;
                if (h % (x - 1) == 0) {
                    found = true;
                } else {
                    x++;
                }
            }
            if (found === false) {
                console.log("no x found.");
                return false;
            }
            if (x > 999) {
                console.log("x too big. x=" + x);
                return false;
            }
            console.log("yes x=" + x);

            var y = Math.round(h / (x - 1));
            if (y < 2 || y > 999) {
                console.log("wrong y with a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
                return false;
            }
            console.log("good y! a=" + a + " for b=" + b + " and y=" + y + " (x=" + x);
            var z = x * y;
            if (z > 999) {
                console.log("wrong z=" + z);
                return false;
            }
            var v = b + y;
            if (v > 999) {
                return false;
            }
            var u = a + x;
            if (u > 999) {
                return false;
            }

            var w = c + z; // = u + v
            if (w > 999) {
                console.log("wrong w=" + w);
                return false;
            }

            var equations = [
                [a, "d", b, "e", c],
                [x, "x", y, "e", z],
                [u, "p", v, "e", w],
                [a, "p", x, "e", u],
                [b, "p", y, "e", v],
                [c, "p", z, "e", w],
            ];
            return equations;
        }

        function create_equations() {
            /*
            var equations = [
                ["1", "p", "3", "e", "4"],
                ["100", "m", "73", "e", "124"],
                ["999", "x", "73", "e", "12"],
                ["1", "p", "3", "e", "4"],
                ["3", "m", "73", "e", "124"],
                ["4", "x", "73", "e", "12"]
            ];
            */
            var attempt = 0;

            var c = false;
            while (c === false && attempt++ < 150) {
                var z = Math.floor(random() * 8);
                <?php
                if (isset($_GET["z"])) {
                    echo "z=" . $_GET["z"] . ";";
                    echo "\r\n";
                }
                ?>
                // console.log("z=" + z);

                var createfun = "create_mode" + z;
                c = window[createfun]();


            }
            <?php
            if (isset($_GET["notransform"])) {
                echo 'return c;';
                echo "\r\n";
            }
            ?>

            if (c !== false) {
                var jmax = Math.floor(random() * 6);
                for (j = 0; j < jmax; j++) {
                    var t = Math.floor(random() * 4);
                    var transfun = "trans_eq" + t;
                    c = window[transfun](c);
                }
            }
            // console.log("JSON=" + JSON.stringify(c));
            return c;
        }


        function addNumber(i, j, n, num_digits = 3) {
            firstround = true;
            while (num_digits > 0) {
                t = document.getElementById("tdiv" + i + "x" + (Math.floor(j) + num_digits - 1));

                d = Math.round(n % 10);
                if (Math.abs(n) < 0.000001 && !firstround) {
                    t.innerHTML = " ";
                } else {
                    t.innerHTML = dig2html(d);
                }
                n = Math.round((n - d) / 10)
                num_digits--;
                firstround = false;
            }
        }

        function getNumber(i, j, num_digits = 3) {
            n = 0;
            s = 1;
            while (num_digits > 0) {
                t = document.getElementById("tdiv" + i + "x" + (Math.floor(j) + num_digits - 1));
                //  console.log("i=" + i + " j=" + j + " tinn=" + t.innerHTML);
                d = html2dig(t);
                // console.log("d=" + d);
                n += (s * d);
                s *= 10;
                num_digits--;
            }
            return n;

        }

        function getEquationAcross(i, jstart, num_digits = 3) {
            var res = [];
            var j = jstart;
            res[0] = getNumber(i, j);
            j += num_digits;
            res[1] = getOp(i, j);
            j += 1;
            res[2] = getNumber(i, j);
            j += num_digits;
            res[3] = getOp(i, j);
            j += 1;
            res[4] = getNumber(i, j);
            return res;
        }

        function addEquationAcross(i, jstart, q, num_digits = 3) {
            var j = jstart;
            addNumber(i, j, q[0]);
            j += num_digits;
            addOp(i, j, q[1]);
            j += 1;
            addNumber(i, j, q[2]);
            j += num_digits;
            addOp(i, j, q[3]);
            j += 1;
            addNumber(i, j, q[4]);
        }

        function addEquationDown(istart, j, q, num_digits = 3) {
            var i = istart;
            var deltaop = Math.floor(num_digits * 0.5);
            addNumber(i, j, q[0]);
            i++;
            addOp(i, j + deltaop, q[1]);
            i++;
            addNumber(i, j, q[2]);
            i++;
            addOp(i, j + deltaop, q[3]);
            i++;
            addNumber(i, j, q[4]);
        }

        function getEquationDown(istart, j, q, num_digits = 3) {
            var res = [];
            var i = istart;
            var deltaop = Math.floor(num_digits * 0.5);
            res[0] = getNumber(i, j);
            i++;
            res[1] = getOp(i, j + deltaop);
            i++;
            res[2] = getNumber(i, j);
            i++;
            res[3] = getOp(i, j + deltaop);
            i++;
            res[4] = getNumber(i, j);
            return res;
        }

        function addAllEquations(i, j, eqs, num_digits = 3) {
            addEquationAcross(i, j, eqs[0], num_digits);
            addEquationAcross(i + 2, j, eqs[1], num_digits);
            addEquationAcross(i + 4, j, eqs[2], num_digits);
            addEquationDown(i, j, eqs[3], num_digits);
            addEquationDown(i, j + num_digits + 1, eqs[4], num_digits);
            addEquationDown(i, j + num_digits + 1 + num_digits + 1, eqs[5], num_digits);
        }

        function getAllEquations(i, j, num_digits = 3) {
            res = [];
            res[0] = getEquationAcross(i, j, num_digits);
            res[1] = getEquationAcross(i + 2, j, num_digits);
            res[2] = getEquationAcross(i + 4, j, num_digits);
            res[3] = getEquationDown(i, j, num_digits);
            res[4] = getEquationDown(i, j + num_digits + 1, num_digits);
            res[5] = getEquationDown(i, j + num_digits + 1 + num_digits + 1, num_digits);
            return res;
        }
        var startup_z = 0;
        var startup_id = null;

        function permalinktothis() {
            var info = document.getElementById("count");
            var url = window.location.href;
            var x = url.split('?');
            var tt = x[0].replace("perisic.com", "sanfoh.com");
            var newurl = tt + '?t=' + Date.now() + '&v=' + version + '&n=' + info.innerHTML;
            var t = document.getElementById("permalink");
            t.href = newurl;

            var ttoday = document.getElementById("permatoday");
            ttoday.href = tt + '?t=' + Date.now() + '&v=' + version + '&n=today';
            console.log(newurl);
        }

        function startup() {
            newgame();
        }

        function newgameserver() {
            var url = window.location.href;
            var x = url.split('?');
            var newurl = x[0] + '?t=' + Date.now();
            console.log(newurl);
            window.location.href = newurl;

        }

        function newgame() {
            var info = document.getElementById("count");
            info.innerHTML = "" + maincount.toFixed(3) + "";
            seed_random();

            maincount = maincount + 0.001;
            equations = create_equations();
            if (equations === false) {
                var info = document.getElementById("info");
                info.innerHTML = "⌛ (" + (startup_z++) + ").";
                clearTimeout(startup_id);
                startup_id = setTimeout("newgame()", 10);
                return null;
            } else {

                var info = document.getElementById("info");
                info.innerHTML = "🏠";
                addAllEquations(0, 0, equations);

                <?php
                if (isset($_GET["noswap"]) === false && isset($_GET["test"]) === false) {
                    echo 'setTimeout("randomswap()", 10);';
                    echo "\r\n";
                }
                if (isset($_GET["test"])) {
                    echo 'setTimeout("testButton()", ' . ($_GET["speed"] ?? 10) . ');';
                    echo "\r\n";
                }
                ?>
                var ng = document.getElementById("newgame");
                ng.style.display = "none";
            }
            var info2 = document.getElementById("info2");
            info2.innerHTML = "Swap digits to make the equations correct!";
            if(datetoday !== '') { 
                info2.innerHTML = "6EQ of: "+datetoday+" (UTC)";
            }
            permalinktothis();

        }

        function copy2clipboard() {
            var t = document.getElementById("permalink");
            var copyText = document.getElementById("permashare");
            copyText.value = t.href;
            copyText.type = 'text';
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            copyText.type = 'hidden';
            window.alert("Copied direct link to this 6EQ to clipboard. Just paste into social media or email to share.");

        }

        function testButton() {

            var x = getAllEquations(0, 0);
            // console.log(JSON.stringify(x));
            for (i = 0; i < 6; i++) {
                var t = check_equation(x[i]);
                if (t === false) {
                    var info = document.getElementById("info");
                    //  info.innerHTML = "FALSE: " + i + ".";
                    info.innerHTML = "💩";
                    var info2 = document.getElementById("info2");
                    info2.innerHTML = "Swap digits to make the equations correct!";
                    var ng = document.getElementById("newgame");
                    ng.style.display = "none";
                    return;
                }

            }
            var info = document.getElementById("info");
            info.innerHTML = "❤️😀👍";
            var info2 = document.getElementById("info2");
            info2.innerHTML = "Well done!";
            var ng = document.getElementById("newgame");

            ng.style.display = "block";
            <?php
            if (isset($_GET["test"])) {
                echo 'setTimeout("newgame()", 10);';
                echo "\r\n";
            }
            ?>

        }
    </script>




    <script>
        function checkAndReport(i, j, num_digits = 3) {
            ta = document.getElementById("outputdebug");
            xa = getAllEquations(i, j);
            ta.innerHTML = JSON.stringify(xa);
        }

        function swapft(finn, tinn) {
            // console.log("finn="+finn+" tinn="+tinn); 
            var tdivs = document.getElementsByClassName("ijdiv");
            for (i = 0; i < tdivs.length; i++) {
                var x = tdivs[i];
                if (x.innerHTML == finn) {
                    x.innerHTML = tinn;
                } else if (x.innerHTML == tinn) {
                    x.innerHTML = finn;
                }
                x.style.background = "white";
                ff = document.getElementById("clickedfirst");
                ff.innerHTML = "F";
            }
            var info = document.getElementById("info");
            // info.innerHTML = "Swap "+finn+" and "+tinn;
            info.innerHTML = "🤔";
            checkAndReport(0, 0);
        }

        function randomswap() {
            var tdivs = document.getElementsByClassName("ijdiv");
            var stdivs = [];
            var k = 0;
            for (j = 0; j < tdivs.length; j++) {
                if (isSwappable(tdivs[j]) !== false) {
                    stdivs[k++] = tdivs[j];
                }
            }
            for (s = 1; s < 3; s++) {
                for (j = 0; j < stdivs.length; j++) {
                    var a = j;
                    var b = Math.floor(random() * stdivs.length);

                    var aa = stdivs[a];
                    var bb = stdivs[b];

                    // if (isSwappable(aa) !== false && isSwappable(bb) !== false) {
                    swapft(aa.innerHTML, bb.innerHTML);
                    // }
                }
            }
        }

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function dragxx(ev) {
            //  console.log("eti=" + ev.target.id);
            ev.dataTransfer.setData("fromdiv", ev.target.id);
            ff = document.getElementById("clickedfirst");
            ff.innerHTML = ev.target.innerHTML;
            ev.dataTransfer.setData("hello", "world");
            var info = document.getElementById("info");
            info.innerHTML = "💭";
        }

        function clicked(ev) {
            //  console.log("click=" + ev.target.id);
            todiv = ev.target;
            if (todiv.id.startsWith("tdiv") === false) {
                todiv = ev.target.parentElement;
            }
            if (isSwappable(todiv) === false) {
                var info = document.getElementById("info");
                info.innerHTML = "❌";
                return;
            }
            ff = document.getElementById("clickedfirst");
            todiv.style.background = "yellow";

            if (ff.innerHTML == "F") {
                ff.innerHTML = todiv.innerHTML;
                todiv.style.background = "pink";
                var info = document.getElementById("info");
                info.innerHTML = "💡";
            } else {
                todiv.style.background = "grey";
                swapft(todiv.innerHTML, ff.innerHTML);

            }

        }

        function drop(ev) {
            ev.preventDefault();
            var fromdiv = ev.dataTransfer.getData("fromdiv");
            var todiv = ev.target.id;
            if (todiv.startsWith("tdiv") === false) {
                todiv = ev.target.parentElement.id;
            }

            // console.log("fromdiv=" + fromdiv + " todiv=" + todiv);
            var f = document.getElementById(fromdiv);
            var t = document.getElementById(todiv);

            if (isSwappable(t) === false || isSwappable(f) === false) {
                var info = document.getElementById("info");
                info.innerHTML = "🛑";
                return;
            }
            swapft(f.innerHTML, t.innerHTML);




            // console.log("(2) fromdiv=" + fromdiv + " todiv=" + todiv);

        }
    </script>


</head>

<body onload="startup()">
    <table width="100px">
        <caption id="captionsix">Six Equations</caption>
        <?php
        for ($i = 0; $i < 5; $i++) {
            echo "<tr>";
            for ($j = 0; $j < 11; $j++) {
                echo "<td id=\"td" . $i . "x" . $j . "\" class=\"ij\" ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\">";

                echo "<div draggable=true onclick=\"clicked(event)\" ondragstart=\"dragxx(event)\" class=\"ijdiv\" id=\"tdiv" . $i . "x" . $j . "\" >";

                echo " ";

                echo "</div>";
                echo "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
    <hr>
    <button id="moreinfobtn" onclick="toggleInfo()">❓</button>
    <button id="sharebtn" onclick="copy2clipboard()">↗️</button>
    <input type="hidden" id="permashare" value="notset" />

    <button id="checkbtn" onclick="testButton()">Check</button>

    <span id="info">[info]</span>
    <p><span id="info2">Swap digits to make the equations correct!</span></p>

    <span hidden id="clickedfirst">F</span>
    <div hidden id="outputdebug">empty</div>
    <p>
        <button id="newgame" onClick="newgame()">New Game</button>
    </p>
    <script>
        function toggleInfo() {
            var x = document.getElementById("moreinfo");
            console.log("x.style.display=" + x.style.display);
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
    </script>
    <div id="moreinfo" style="display:none">
        <p>
            Click on a digit; then click another digit to swap the two digits. For instance, if you click on <em>any</em> of the
            <span class="digit">'1'</span> and then at <em>any</em> of the <span class="digit">'9'</span> you swap all
            the <span class="digit">'1'</span> with all the <span class="digit">'9'</span> in this game.
        </p>
        <p>
            When you think that all six equations are correct press the 'Check' button.
        </p>
        <p>If your device has a mouse, you can also drag and drop.</p>
        <p>
            Each 6EQ Game has a unique id. Press <a id="permalink" href="none"><button id="count"></button></a> to open a permalink to this specific game.

        </p>
        <p>When you have solved the 6EQ game you will be given the opportunity to play a new game. The new game is dynamically created in your browser (so you can play offline).
            Use this button to download a new game from the server:
            <button id="newgameserverbtn" onClick="newgameserver()">New Game (Server)</button>
        </p>
        <p>For a permanent link to the game of 'today' (every day a new game) use this link: 
                                <a id="permatoday" href="none"><button id="permatoday">6EQ of today</button></a>. 
                                When you bookmark that link or add it to your home screen you have a new game every day! 
            
        </p>
        <hr>

        <p>
            <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons Licence" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>.
        </p>
        <p>
            <small>
                &copy; by <a href="http://dr.marcconrad.com/">Marc Conrad</a> 2021.
                The material on this page is presented &quot;as is&quot;. There is no warranty implied.
            </small>
        </p>
    </div>
</body>

</html>