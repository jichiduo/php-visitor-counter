# Simple PHP Visitor Counter

Simple single file PHP visitor counter. Put it to your website PHP code folder and you are ready to go. This counter is just for personal or small scale websites.

### Features

1. Pure PHP, No other lib required, No configuration required
2. Fast, Simple and Easy to use
3. No database required, visitor's data store in a csv file
4. File size is less than 5kb 

### How to use

Learn how to use this counter

#### Requirement

- PHP7 and above
- Write permission of the counter log file 

``` bash
#in the same directory as vcounter.php or your can define your csv file folder 
touch vstCnt.txt
sudo chmod 666 vstCnt.txt
```

#### Installation

Just put vcounter.php in your php src folder or wherever place it can be runned 

In the homepage or the page need to count the visitor just try the following method to use it

``` Javascript
<script type="text/javascript" src="vcounter.php?p=js"></script>     
```
### License

This code is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
