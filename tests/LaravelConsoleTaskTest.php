<?php

declare(strict_types=1);

/**
 * This file is part of Laravel Console Task.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Tests\LaravelConsoleTask;

use ReflectionClass;
use Illuminate\Console\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;

/**
 * This is the service provider test class.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class LaravelConsoleTaskTest extends TestCase
{
    public function testSuccessfulTaskWithReturnValueAndDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithDecoratedOutput(
            function () {
                return true;
            },
            '✔'
        );
    }

    public function testSuccessfulTaskWithoutReturnValueAndDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithDecoratedOutput(
            function () {
            },
            '✔'
        );
    }

    public function testSuccessfulTaskWithCustomReturnValueAndDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithDecoratedOutput(
            function () {
                return 'ok';
            },
            'ok'
        );
    }

    private function performTestSuccessfulTaskWithDecoratedOutput(callable $task, string $expectedResult)
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Foo: <comment>loading...</comment>')],
                [$this->equalTo("\x0D")],
                [$this->equalTo("\x1B[2K")]
            );

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Foo: <info>'.$expectedResult.'</info>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', $task)
        );
    }

    public function testSuccessfulTaskWithReturnValueAndWithoutDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithoutDecoratedOutput(
            function () {
                return true;
            },
            '✔'
        );
    }

    public function testSuccessfulTaskWithoutReturnValueAndWithoutDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithoutDecoratedOutput(
            function () {
            },
            '✔'
        );
    }

    public function testSuccessfulTaskWithCustomReturnValueAndWithoutDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithoutDecoratedOutput(
            function () {
                return 'ok';
            },
            'ok'
        );
    }

    private function performTestSuccessfulTaskWithoutDecoratedOutput(callable $task, string $expectedResult)
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Foo: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Foo: <info>'.$expectedResult.'</info>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', $task)
        );
    }

    public function testUnsuccessfulTaskWithDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Bar: <comment>loading...</comment>')],
                [$this->equalTo("\x0D")],
                [$this->equalTo("\x1B[2K")]
            );

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Bar: <error>failed</error>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                }
            )
        );
    }

    public function testUnsuccessfulTaskWithDecoratedOutputAndCustomFailedMessage()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Bar: <comment>loading...</comment>')],
                [$this->equalTo("\x0D")],
                [$this->equalTo("\x1B[2K")]
            );

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Bar: <error>something went wrong</error>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                },
                'loading...',
                'something went wrong'
            )
        );
    }

    public function testUnsuccessfulTaskWithoutDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>failed</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                }
            )
        );
    }

    public function testUnsuccessfulTaskWithoutDecoratedOutputAndCustomFailedMessage()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>something went wrong</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                },
                'loading...',
                'something went wrong'
            )
        );
    }

    public function testUnsuccessfulTaskWithException()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>failed</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->expectException(\Exception::class);

        $command->task(
            'Bar',
            function () {
                throw new \Exception();
            }
        );
    }

    public function testUnsuccessfulTaskWithExceptionAndCustomFailedMessage()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputInterface::class);

        $outputMock->expects($this->once())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>something went wrong</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->expectException(\Exception::class);

        $command->task(
            'Bar',
            function () {
                throw new \Exception();
            },
            'loading...',
            'something went wrong'
        );
    }
}
