package main

import (
    "fmt"
    "sync"
    "os/exec"
)

func phpWorker(script string) {
    app := "php"

    arg0 := script

    fmt.Println("Run command: ", "php ", script)

    cmd := exec.Command(app, arg0)
    stdout, err := cmd.Output()

    if err != nil {
        fmt.Println(err.Error())
        return
    }

    fmt.Println(string(stdout))
}

func main() {

    wg := &sync.WaitGroup{}

    scripts := []string{"1.php", "2.php"}

    for _, script := range scripts {
        wg.Add(1)

        script := script

        go func() {
            defer wg.Done()
            phpWorker(script)
        }()
    }

    wg.Wait()

}